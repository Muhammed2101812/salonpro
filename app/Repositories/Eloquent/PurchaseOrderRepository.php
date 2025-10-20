<?php

namespace App\Repositories\Eloquent;

use App\Models\PurchaseOrder;
use App\Repositories\Contracts\PurchaseOrderRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class PurchaseOrderRepository implements PurchaseOrderRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return PurchaseOrder::with(['supplier', 'items.product', 'branch'])
            ->orderBy('order_date', 'desc')
            ->paginate($perPage);
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return PurchaseOrder::with(['supplier', 'items.product', 'branch'])
            ->orderBy('order_date', 'desc')
            ->get();
    }

    public function findById(int $id): ?PurchaseOrder
    {
        return PurchaseOrder::with(['supplier', 'items.product', 'branch'])
            ->find($id);
    }

    public function create(array $data): PurchaseOrder
    {
        return PurchaseOrder::create($data);
    }

    public function update(PurchaseOrder $purchaseOrder, array $data): bool
    {
        return $purchaseOrder->update($data);
    }

    public function delete(PurchaseOrder $purchaseOrder): bool
    {
        return $purchaseOrder->delete();
    }

    public function getByStatus(string $status): \Illuminate\Database\Eloquent\Collection
    {
        return PurchaseOrder::with(['supplier', 'items.product'])
            ->where('status', $status)
            ->orderBy('order_date', 'desc')
            ->get();
    }

    public function getBySupplier(int $supplierId): \Illuminate\Database\Eloquent\Collection
    {
        return PurchaseOrder::with(['items.product'])
            ->where('supplier_id', $supplierId)
            ->orderBy('order_date', 'desc')
            ->get();
    }

    public function updateStatus(PurchaseOrder $purchaseOrder, string $status): bool
    {
        return $purchaseOrder->update(['status' => $status]);
    }

    public function getPending(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->getByStatus('pending');
    }
}
