<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\PurchaseOrderRepositoryInterface;
use App\Services\Contracts\PurchaseOrderServiceInterface;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService extends BaseService implements PurchaseOrderServiceInterface
{
    public function __construct(
        protected PurchaseOrderRepositoryInterface $purchaseOrderRepository
    ) {
        parent::__construct($purchaseOrderRepository);
    }

    public function getByBranch(string $branchId, int $perPage = 15): mixed
    {
        return $this->purchaseOrderRepository->findByBranch($branchId, $perPage);
    }

    public function getBySupplier(string $supplierId, int $perPage = 15): mixed
    {
        return $this->purchaseOrderRepository->findBySupplier($supplierId, $perPage);
    }

    public function getPending(?string $branchId = null): mixed
    {
        return $this->purchaseOrderRepository->getPending($branchId);
    }

    public function getOverdue(?string $branchId = null): mixed
    {
        return $this->purchaseOrderRepository->getOverdue($branchId);
    }

    public function createWithItems(array $data): mixed
    {
        return DB::transaction(function () use ($data) {
            // Generate order number if not provided
            if (!isset($data['order_number'])) {
                $data['order_number'] = $this->purchaseOrderRepository->generateOrderNumber();
            }

            // Set default status
            if (!isset($data['status'])) {
                $data['status'] = 'pending';
            }

            // Extract items
            $items = $data['items'] ?? [];
            unset($data['items']);

            // Create purchase order
            $purchaseOrder = $this->purchaseOrderRepository->create($data);

            // Create items
            if (!empty($items)) {
                foreach ($items as $item) {
                    $item['purchase_order_id'] = $purchaseOrder->id;
                    $purchaseOrder->items()->create($item);
                }
            }

            return $purchaseOrder->load(['items', 'supplier', 'branch', 'creator']);
        });
    }

    public function updateStatus(string $id, string $status): mixed
    {
        return $this->purchaseOrderRepository->update($id, ['status' => $status]);
    }

    public function receive(string $id, array $data): mixed
    {
        return DB::transaction(function () use ($id, $data) {
            $updateData = [
                'status' => 'received',
                'actual_delivery_date' => $data['delivery_date'] ?? now(),
            ];

            return $this->purchaseOrderRepository->update($id, $updateData);
        });
    }

    public function cancel(string $id, ?string $reason = null): mixed
    {
        return DB::transaction(function () use ($id, $reason) {
            $updateData = [
                'status' => 'cancelled',
            ];

            if ($reason) {
                $updateData['notes'] = ($updateData['notes'] ?? '') . "\nCancellation reason: {$reason}";
            }

            return $this->purchaseOrderRepository->update($id, $updateData);
        });
    }

    public function getTotalsByPeriod(string $startDate, string $endDate, ?string $branchId = null): array
    {
        return $this->purchaseOrderRepository->getTotalsByPeriod($startDate, $endDate, $branchId);
    }
}
