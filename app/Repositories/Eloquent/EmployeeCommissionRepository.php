<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\EmployeeCommission;
use App\Repositories\Contracts\EmployeeCommissionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EmployeeCommissionRepository extends BaseRepository implements EmployeeCommissionRepositoryInterface
{
    public function __construct(EmployeeCommission $model)
    {
        parent::__construct($model);
    }

    public function findByEmployee(string $employeeId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('employee_id', $employeeId)
            ->with(['employee', 'appointment', 'sale'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findByStatus(string $status, ?string $employeeId = null): Collection
    {
        $query = $this->model->where('payment_status', $status)
            ->with(['employee', 'appointment', 'sale']);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function findUnpaid(?string $employeeId = null): Collection
    {
        return $this->findByStatus('unpaid', $employeeId);
    }

    public function findByDateRange(string $startDate, string $endDate, ?string $employeeId = null): Collection
    {
        $query = $this->model->whereBetween('created_at', [$startDate, $endDate])
            ->with(['employee', 'appointment', 'sale']);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getSummary(string $employeeId, string $startDate, string $endDate): array
    {
        $commissions = $this->findByDateRange($startDate, $endDate, $employeeId);

        return [
            'total_commissions' => $commissions->count(),
            'total_amount' => $commissions->sum('commission_amount'),
            'paid_amount' => $commissions->where('payment_status', 'paid')->sum('commission_amount'),
            'unpaid_amount' => $commissions->where('payment_status', 'unpaid')->sum('commission_amount'),
            'average_commission' => $commissions->avg('commission_amount'),
            'by_type' => $commissions->groupBy('commission_type')
                ->map(fn($group) => [
                    'count' => $group->count(),
                    'total' => $group->sum('commission_amount'),
                ]),
        ];
    }

    public function getTotalUnpaid(?string $employeeId = null): float
    {
        $query = $this->model->where('payment_status', 'unpaid');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        return (float) $query->sum('commission_amount');
    }
}
