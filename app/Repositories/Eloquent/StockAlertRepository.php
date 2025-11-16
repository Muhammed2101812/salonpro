<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\StockAlert;
use App\Repositories\Contracts\StockAlertRepositoryInterface;
use Illuminate\Support\Collection;

class StockAlertRepository extends BaseRepository implements StockAlertRepositoryInterface
{
    public function __construct(StockAlert $model)
    {
        parent::__construct($model);
    }

    public function findByBranch(string $branchId, int $perPage = 15): mixed
    {
        return $this->model->with(['branch', 'product'])
            ->where('branch_id', $branchId)
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findByProduct(string $productId): Collection
    {
        return $this->model->with(['branch', 'product'])
            ->where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getActive(?string $branchId = null): Collection
    {
        $query = $this->model->with(['branch', 'product'])
            ->where('status', 'active')
            ->whereNull('resolved_at')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    public function getResolved(?string $branchId = null): Collection
    {
        $query = $this->model->with(['branch', 'product'])
            ->where('status', 'resolved')
            ->whereNotNull('resolved_at')
            ->orderBy('resolved_at', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    public function getByType(string $alertType, ?string $branchId = null): Collection
    {
        $query = $this->model->with(['branch', 'product'])
            ->where('alert_type', $alertType)
            ->orderBy('created_at', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    public function getByPriority(int $priority, ?string $branchId = null): Collection
    {
        $query = $this->model->with(['branch', 'product'])
            ->where('priority', $priority)
            ->orderBy('created_at', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    public function markAsNotified(string $id): mixed
    {
        return $this->update($id, [
            'notified_at' => now(),
        ]);
    }

    public function markAsResolved(string $id, ?string $notes = null): mixed
    {
        $data = [
            'status' => 'resolved',
            'resolved_at' => now(),
        ];

        if ($notes) {
            $data['notes'] = $notes;
        }

        return $this->update($id, $data);
    }

    public function getCriticalAlerts(?string $branchId = null): Collection
    {
        $query = $this->model->with(['branch', 'product'])
            ->where('status', 'active')
            ->whereNull('resolved_at')
            ->where('priority', '>=', 3)
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }
}
