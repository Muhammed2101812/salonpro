<?php

namespace App\Services;

use App\Models\StockTransfer;
use App\Repositories\Contracts\StockTransferRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockTransferService
{
    public function __construct(
        private StockTransferRepositoryInterface $stockTransferRepository,
        private ProductRepositoryInterface $productRepository
    ) {}

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->stockTransferRepository->getAllPaginated($perPage);
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->stockTransferRepository->getAll();
    }

    public function findById(int $id): ?StockTransfer
    {
        return $this->stockTransferRepository->findById($id);
    }

    public function create(array $data): StockTransfer
    {
        $data['requested_by'] = $data['requested_by'] ?? Auth::id();
        $data['status'] = 'pending';
        $data['transfer_date'] = $data['transfer_date'] ?? now();

        return $this->stockTransferRepository->create($data);
    }

    public function update(int $id, array $data): StockTransfer
    {
        $transfer = $this->stockTransferRepository->findById($id);
        
        if (!$transfer) {
            throw new \Exception('Stok transferi bulunamadı');
        }

        // Cannot update if already completed or cancelled
        if (in_array($transfer->status, ['completed', 'cancelled'])) {
            throw new \Exception('Tamamlanmış veya iptal edilmiş transferler güncellenemez');
        }

        $this->stockTransferRepository->update($transfer, $data);
        
        return $transfer->fresh();
    }

    public function delete(int $id): bool
    {
        $transfer = $this->stockTransferRepository->findById($id);
        
        if (!$transfer) {
            throw new \Exception('Stok transferi bulunamadı');
        }

        // Can only delete pending transfers
        if ($transfer->status !== 'pending') {
            throw new \Exception('Sadece bekleyen transferler silinebilir');
        }

        return $this->stockTransferRepository->delete($transfer);
    }

    public function approve(int $id): StockTransfer
    {
        return DB::transaction(function () use ($id) {
            $transfer = $this->stockTransferRepository->findById($id);
            
            if (!$transfer) {
                throw new \Exception('Stok transferi bulunamadı');
            }

            if ($transfer->status !== 'pending') {
                throw new \Exception('Sadece bekleyen transferler onaylanabilir');
            }

            // Check if source branch has enough stock
            $product = $this->productRepository->findById($transfer->product_id);
            
            if (!$product) {
                throw new \Exception('Ürün bulunamadı');
            }

            // For branch-scoped products, we need to check the specific branch's stock
            // This is a simplified check - you may need to adjust based on your exact needs
            if ($product->quantity < $transfer->quantity) {
                throw new \Exception('Kaynak şubede yeterli stok bulunmamaktadır');
            }

            // Deduct from source branch
            $this->productRepository->update($product, [
                'quantity' => $product->quantity - $transfer->quantity
            ]);

            // Add to destination branch
            // Note: This assumes products are branch-specific
            // You may need to find or create the product for the destination branch
            $destinationProduct = \App\Models\Product::where('branch_id', $transfer->to_branch_id)
                ->where('name', $product->name)
                ->where('sku', $product->sku)
                ->first();

            if ($destinationProduct) {
                $this->productRepository->update($destinationProduct, [
                    'quantity' => $destinationProduct->quantity + $transfer->quantity
                ]);
            }

            // Update transfer
            $this->stockTransferRepository->update($transfer, [
                'status' => 'completed',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            return $transfer->fresh();
        });
    }

    public function reject(int $id, ?string $reason = null): StockTransfer
    {
        $transfer = $this->stockTransferRepository->findById($id);
        
        if (!$transfer) {
            throw new \Exception('Stok transferi bulunamadı');
        }

        if ($transfer->status !== 'pending') {
            throw new \Exception('Sadece bekleyen transferler reddedilebilir');
        }

        $this->stockTransferRepository->update($transfer, [
            'status' => 'cancelled',
            'notes' => $reason,
        ]);

        return $transfer->fresh();
    }

    public function getByStatus(string $status): \Illuminate\Database\Eloquent\Collection
    {
        return $this->stockTransferRepository->getByStatus($status);
    }

    public function getFromBranch(int $branchId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->stockTransferRepository->getFromBranch($branchId);
    }

    public function getToBranch(int $branchId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->stockTransferRepository->getToBranch($branchId);
    }

    public function getPending(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->stockTransferRepository->getPending();
    }
}
