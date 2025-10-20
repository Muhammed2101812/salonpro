<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Repositories\Contracts\PurchaseOrderRepositoryInterface;
use App\Repositories\Contracts\PurchaseOrderItemRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService
{
    public function __construct(
        private PurchaseOrderRepositoryInterface $purchaseOrderRepository,
        private PurchaseOrderItemRepositoryInterface $itemRepository,
        private ProductRepositoryInterface $productRepository
    ) {}

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->purchaseOrderRepository->getAllPaginated($perPage);
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->purchaseOrderRepository->getAll();
    }

    public function findById(int $id): ?PurchaseOrder
    {
        return $this->purchaseOrderRepository->findById($id);
    }

    public function create(array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];
            unset($data['items']);

            // Generate order number
            $data['order_number'] = $this->generateOrderNumber();
            $data['status'] = $data['status'] ?? 'pending';

            $purchaseOrder = $this->purchaseOrderRepository->create($data);

            // Create items
            if (!empty($items)) {
                $this->itemRepository->createMultiple($purchaseOrder->id, $items);
            }

            return $purchaseOrder->fresh(['items.product', 'supplier']);
        });
    }

    public function update(int $id, array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($id, $data) {
            $purchaseOrder = $this->purchaseOrderRepository->findById($id);
            
            if (!$purchaseOrder) {
                throw new \Exception('Satın alma siparişi bulunamadı');
            }

            // Cannot update if already completed or cancelled
            if (in_array($purchaseOrder->status, ['completed', 'cancelled'])) {
                throw new \Exception('Tamamlanmış veya iptal edilmiş siparişler güncellenemez');
            }

            $items = $data['items'] ?? null;
            unset($data['items']);

            $this->purchaseOrderRepository->update($purchaseOrder, $data);

            // Update items if provided
            if ($items !== null) {
                // Delete existing items
                foreach ($purchaseOrder->items as $item) {
                    $this->itemRepository->delete($item);
                }
                
                // Create new items
                $this->itemRepository->createMultiple($purchaseOrder->id, $items);
            }

            return $purchaseOrder->fresh(['items.product', 'supplier']);
        });
    }

    public function delete(int $id): bool
    {
        $purchaseOrder = $this->purchaseOrderRepository->findById($id);
        
        if (!$purchaseOrder) {
            throw new \Exception('Satın alma siparişi bulunamadı');
        }

        // Can only delete pending orders
        if ($purchaseOrder->status !== 'pending') {
            throw new \Exception('Sadece bekleyen siparişler silinebilir');
        }

        return DB::transaction(function () use ($purchaseOrder) {
            // Delete items
            foreach ($purchaseOrder->items as $item) {
                $this->itemRepository->delete($item);
            }

            return $this->purchaseOrderRepository->delete($purchaseOrder);
        });
    }

    public function updateStatus(int $id, string $status): PurchaseOrder
    {
        $purchaseOrder = $this->purchaseOrderRepository->findById($id);
        
        if (!$purchaseOrder) {
            throw new \Exception('Satın alma siparişi bulunamadı');
        }

        $this->purchaseOrderRepository->updateStatus($purchaseOrder, $status);

        return $purchaseOrder->fresh();
    }

    public function receive(int $id, array $receivedItems): PurchaseOrder
    {
        return DB::transaction(function () use ($id, $receivedItems) {
            $purchaseOrder = $this->purchaseOrderRepository->findById($id);
            
            if (!$purchaseOrder) {
                throw new \Exception('Satın alma siparişi bulunamadı');
            }

            if ($purchaseOrder->status === 'completed') {
                throw new \Exception('Bu sipariş zaten tamamlanmış');
            }

            // Update received quantities and stock
            foreach ($receivedItems as $itemId => $quantity) {
                $item = $this->itemRepository->findById($itemId);
                
                if ($item && $item->purchase_order_id === $purchaseOrder->id) {
                    $this->itemRepository->updateReceivedQuantity($item, $quantity);
                    
                    // Update product stock
                    $product = $this->productRepository->findById($item->product_id);
                    if ($product) {
                        $this->productRepository->update($product, [
                            'quantity' => $product->quantity + $quantity
                        ]);
                    }
                }
            }

            // Check if all items are fully received
            $allReceived = true;
            foreach ($purchaseOrder->fresh()->items as $item) {
                if ($item->received_quantity < $item->quantity) {
                    $allReceived = false;
                    break;
                }
            }

            // Update order status
            $newStatus = $allReceived ? 'completed' : 'partial';
            $this->purchaseOrderRepository->updateStatus($purchaseOrder, $newStatus);

            return $purchaseOrder->fresh(['items.product', 'supplier']);
        });
    }

    public function getByStatus(string $status): \Illuminate\Database\Eloquent\Collection
    {
        return $this->purchaseOrderRepository->getByStatus($status);
    }

    public function getBySupplier(int $supplierId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->purchaseOrderRepository->getBySupplier($supplierId);
    }

    public function getPending(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->purchaseOrderRepository->getPending();
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'PO';
        $date = now()->format('Ymd');
        $lastOrder = PurchaseOrder::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastOrder ? (intval(substr($lastOrder->order_number, -4)) + 1) : 1;

        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
