<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SupplierRepository extends BaseRepository implements SupplierRepositoryInterface
{
    public function __construct(Supplier $model)
    {
        parent::__construct($model);
    }

    public function findActive(): Collection
    {
        return $this->model->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function findByCity(string $city, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('city', $city)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findByCountry(string $country, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('country', $country)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('contact_person', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%");
        })
        ->orderBy('name')
        ->paginate($perPage);
    }

    public function getStats(string $supplierId): array
    {
        $supplier = $this->findOrFail($supplierId);

        return [
            'total_purchase_orders' => $supplier->purchaseOrders()->count(),
            'total_amount_spent' => $supplier->purchaseOrders()
                ->where('status', 'completed')
                ->sum('final_amount'),
            'pending_orders' => $supplier->purchaseOrders()
                ->where('status', 'pending')
                ->count(),
            'last_order_date' => $supplier->purchaseOrders()
                ->latest('order_date')
                ->value('order_date'),
        ];
    }
}
