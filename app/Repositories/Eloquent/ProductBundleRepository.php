<?php

namespace App\Repositories\Eloquent;

use App\Models\ProductBundle;
use App\Repositories\Contracts\ProductBundleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductBundleRepository implements ProductBundleRepositoryInterface
{
    public function __construct(
        protected ProductBundle $model
    ) {}

    public function all(): Collection
    {
        return $this->model
            ->with(['branch', 'items.product'])
            ->latest()
            ->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query()->with(['branch', 'items.product']);

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('start_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('end_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function findById(int $id): ?ProductBundle
    {
        return $this->model
            ->with(['branch', 'items.product'])
            ->find($id);
    }

    public function create(array $data): ProductBundle
    {
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];
            unset($data['items']);

            $bundle = $this->model->create($data);

            if (!empty($items)) {
                foreach ($items as $item) {
                    $bundle->items()->create($item);
                }
            }

            return $bundle->load(['branch', 'items.product']);
        });
    }

    public function update(int $id, array $data): ProductBundle
    {
        return DB::transaction(function () use ($id, $data) {
            $bundle = $this->findById($id);
            
            if (!$bundle) {
                throw new \Exception('Ürün paketi bulunamadı.');
            }

            $items = $data['items'] ?? null;
            unset($data['items']);

            $bundle->update($data);

            if ($items !== null) {
                $bundle->items()->delete();
                foreach ($items as $item) {
                    $bundle->items()->create($item);
                }
            }

            return $bundle->fresh(['branch', 'items.product']);
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $bundle = $this->findById($id);
            
            if (!$bundle) {
                throw new \Exception('Ürün paketi bulunamadı.');
            }

            $bundle->items()->delete();
            return $bundle->delete();
        });
    }

    public function findByBranch(int $branchId): Collection
    {
        return $this->model
            ->with(['items.product'])
            ->where('branch_id', $branchId)
            ->latest()
            ->get();
    }

    public function findActive(): Collection
    {
        return $this->model
            ->with(['branch', 'items.product'])
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_date')
                      ->orWhereDate('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                      ->orWhereDate('end_date', '>=', now());
            })
            ->latest()
            ->get();
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model
            ->with(['branch', 'items.product'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->latest()
            ->get();
    }

    public function activate(int $id): ProductBundle
    {
        $bundle = $this->findById($id);
        
        if (!$bundle) {
            throw new \Exception('Ürün paketi bulunamadı.');
        }

        $bundle->update(['is_active' => true]);

        return $bundle->fresh(['branch', 'items.product']);
    }

    public function deactivate(int $id): ProductBundle
    {
        $bundle = $this->findById($id);
        
        if (!$bundle) {
            throw new \Exception('Ürün paketi bulunamadı.');
        }

        $bundle->update(['is_active' => false]);

        return $bundle->fresh(['branch', 'items.product']);
    }

    public function checkAvailability(int $id): bool
    {
        $bundle = $this->findById($id);
        
        if (!$bundle || !$bundle->is_active) {
            return false;
        }

        // Check date range
        if ($bundle->start_date && $bundle->start_date > now()) {
            return false;
        }

        if ($bundle->end_date && $bundle->end_date < now()) {
            return false;
        }

        // Check if all products have sufficient stock
        foreach ($bundle->items as $item) {
            if ($item->product->quantity < $item->quantity) {
                return false;
            }
        }

        return true;
    }
}
