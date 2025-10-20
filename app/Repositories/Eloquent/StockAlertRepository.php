<?php

namespace App\Repositories\Eloquent;

use App\Models\StockAlert;
use App\Repositories\Contracts\StockAlertRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class StockAlertRepository implements StockAlertRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return StockAlert::with(['product', 'branch'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return StockAlert::with(['product', 'branch'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findById(int $id): ?StockAlert
    {
        return StockAlert::with(['product', 'branch'])
            ->find($id);
    }

    public function create(array $data): StockAlert
    {
        return StockAlert::create($data);
    }

    public function update(StockAlert $alert, array $data): bool
    {
        return $alert->update($data);
    }

    public function delete(StockAlert $alert): bool
    {
        return $alert->delete();
    }

    public function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return StockAlert::with(['product', 'branch'])
            ->where('is_resolved', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByProduct(int $productId): \Illuminate\Database\Eloquent\Collection
    {
        return StockAlert::with(['branch'])
            ->where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function markAsResolved(StockAlert $alert): bool
    {
        return $alert->update([
            'is_resolved' => true,
            'resolved_at' => now(),
        ]);
    }

    public function getUnresolved(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->getActive();
    }
}
