<?php

namespace App\Repositories\Eloquent;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierRepository implements SupplierRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Supplier::with(['products'])
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Supplier::with(['products'])
            ->orderBy('name')
            ->get();
    }

    public function findById(int $id): ?Supplier
    {
        return Supplier::with(['products', 'purchaseOrders'])
            ->find($id);
    }

    public function create(array $data): Supplier
    {
        return Supplier::create($data);
    }

    public function update(Supplier $supplier, array $data): bool
    {
        return $supplier->update($data);
    }

    public function delete(Supplier $supplier): bool
    {
        return $supplier->delete();
    }

    public function search(string $query): \Illuminate\Database\Eloquent\Collection
    {
        return Supplier::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->orWhere('company', 'like', "%{$query}%")
            ->orderBy('name')
            ->get();
    }

    public function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Supplier::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getWithLowStock(): \Illuminate\Database\Eloquent\Collection
    {
        return Supplier::whereHas('products', function ($query) {
            $query->whereRaw('quantity <= min_quantity');
        })->with(['products' => function ($query) {
            $query->whereRaw('quantity <= min_quantity');
        }])->get();
    }
}
