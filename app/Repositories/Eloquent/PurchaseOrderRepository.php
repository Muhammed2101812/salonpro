<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\PurchaseOrder;
use App\Repositories\Contracts\PurchaseOrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PurchaseOrderRepository extends BaseRepository implements PurchaseOrderRepositoryInterface
{
    public function __construct(PurchaseOrder $model)
    {
        parent::__construct($model);
    }

    public function findByBranch(string $branchId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('branch_id', $branchId)
            ->with(['supplier', 'branch', 'creator', 'items'])
            ->orderBy('order_date', 'desc')
            ->paginate($perPage);
    }

    public function findBySupplier(string $supplierId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('supplier_id', $supplierId)
            ->with(['supplier', 'branch', 'creator', 'items'])
            ->orderBy('order_date', 'desc')
            ->paginate($perPage);
    }

    public function findByStatus(string $status, ?string $branchId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->where('status', $status)
            ->with(['supplier', 'branch', 'creator', 'items']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('order_date', 'desc')->paginate($perPage);
    }

    public function getPending(?string $branchId = null): Collection
    {
        $query = $this->model->where('status', 'pending')
            ->with(['supplier', 'branch', 'creator']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('order_date')->get();
    }

    public function getOverdue(?string $branchId = null): Collection
    {
        $query = $this->model->where('status', 'pending')
            ->where('expected_delivery_date', '<', now())
            ->with(['supplier', 'branch', 'creator']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('expected_delivery_date')->get();
    }

    public function generateOrderNumber(): string
    {
        $prefix = 'PO';
        $date = now()->format('Ymd');
        $count = $this->model->whereDate('created_at', now())->count() + 1;
        $sequence = str_pad((string) $count, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$date}-{$sequence}";
    }

    public function getTotalsByPeriod(string $startDate, string $endDate, ?string $branchId = null): array
    {
        $query = $this->model->whereBetween('order_date', [$startDate, $endDate]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return [
            'total_orders' => $query->count(),
            'total_amount' => $query->sum('final_amount'),
            'by_status' => $query->groupBy('status')
                ->selectRaw('status, count(*) as count, sum(final_amount) as total')
                ->get()
                ->keyBy('status')
                ->toArray(),
        ];
    }
}
