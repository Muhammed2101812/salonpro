<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\EmployeeLeave;
use App\Repositories\Contracts\EmployeeLeaveRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EmployeeLeaveRepository extends BaseRepository implements EmployeeLeaveRepositoryInterface
{
    public function __construct(EmployeeLeave $model)
    {
        parent::__construct($model);
    }

    public function findByEmployee(string $employeeId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('employee_id', $employeeId)
            ->with(['employee', 'approvedBy'])
            ->orderBy('start_date', 'desc')
            ->paginate($perPage);
    }

    public function findByStatus(string $status, ?string $employeeId = null): Collection
    {
        $query = $this->model->where('status', $status)
            ->with(['employee', 'approvedBy']);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        return $query->orderBy('start_date', 'desc')->get();
    }

    public function findPending(?string $employeeId = null): Collection
    {
        return $this->findByStatus('pending', $employeeId);
    }

    public function findByDateRange(string $startDate, string $endDate, ?string $employeeId = null): Collection
    {
        $query = $this->model->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        })->with(['employee', 'approvedBy']);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        return $query->orderBy('start_date', 'desc')->get();
    }

    public function findOverlapping(string $employeeId, string $startDate, string $endDate): Collection
    {
        return $this->model->where('employee_id', $employeeId)
            ->where('status', '!=', 'rejected')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            })
            ->get();
    }

    public function getSummary(string $employeeId, string $year): array
    {
        $leaves = $this->model->where('employee_id', $employeeId)
            ->whereYear('start_date', $year)
            ->where('status', 'approved')
            ->get();

        return [
            'total_leaves' => $leaves->count(),
            'total_days' => $leaves->sum('total_days'),
            'by_type' => $leaves->groupBy('leave_type')
                ->map(fn($group) => [
                    'count' => $group->count(),
                    'total_days' => $group->sum('total_days'),
                ]),
        ];
    }
}
