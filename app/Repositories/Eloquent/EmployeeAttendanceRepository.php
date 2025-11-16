<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\EmployeeAttendance;
use App\Repositories\Contracts\EmployeeAttendanceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EmployeeAttendanceRepository extends BaseRepository implements EmployeeAttendanceRepositoryInterface
{
    public function __construct(EmployeeAttendance $model)
    {
        parent::__construct($model);
    }

    public function findByEmployee(string $employeeId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('employee_id', $employeeId)
            ->with(['employee', 'branch'])
            ->orderBy('clock_in', 'desc')
            ->paginate($perPage);
    }

    public function findByBranch(string $branchId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('branch_id', $branchId)
            ->with(['employee', 'branch'])
            ->orderBy('clock_in', 'desc')
            ->paginate($perPage);
    }

    public function findByDateRange(string $startDate, string $endDate, ?string $employeeId = null): Collection
    {
        $query = $this->model->whereBetween('clock_in', [$startDate, $endDate])
            ->with(['employee', 'branch']);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        return $query->orderBy('clock_in', 'desc')->get();
    }

    public function findToday(?string $branchId = null): Collection
    {
        $query = $this->model->whereDate('clock_in', now()->toDateString())
            ->with(['employee', 'branch']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('clock_in', 'desc')->get();
    }

    public function findActive(?string $branchId = null): Collection
    {
        $query = $this->model->whereNull('clock_out')
            ->with(['employee', 'branch']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('clock_in', 'desc')->get();
    }

    public function getSummary(string $employeeId, string $startDate, string $endDate): array
    {
        $attendances = $this->findByDateRange($startDate, $endDate, $employeeId);

        return [
            'total_days' => $attendances->count(),
            'total_hours' => $attendances->sum('total_hours'),
            'average_hours' => $attendances->avg('total_hours'),
            'late_arrivals' => $attendances->where('status', 'late')->count(),
            'early_departures' => $attendances->where('status', 'early_departure')->count(),
        ];
    }
}
