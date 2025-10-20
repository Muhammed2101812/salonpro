<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\EmployeeShift;
use App\Repositories\Contracts\EmployeeShiftRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class EmployeeShiftRepository implements EmployeeShiftRepositoryInterface
{
    public function all(): Collection
    {
        return EmployeeShift::with(['employee', 'branch'])->get();
    }

    public function find(string $id): ?EmployeeShift
    {
        return EmployeeShift::with(['employee', 'branch'])->find($id);
    }

    public function create(array $data): EmployeeShift
    {
        return EmployeeShift::create($data);
    }

    public function update(string $id, array $data): bool
    {
        return EmployeeShift::where('id', $id)->update($data);
    }

    public function delete(string $id): bool
    {
        return EmployeeShift::where('id', $id)->delete();
    }

    public function getByEmployee(string $employeeId): Collection
    {
        return EmployeeShift::with('branch')
            ->where('employee_id', $employeeId)
            ->orderBy('shift_date', 'desc')
            ->orderBy('start_time')
            ->get();
    }

    public function getByBranch(string $branchId): Collection
    {
        return EmployeeShift::with('employee')
            ->where('branch_id', $branchId)
            ->orderBy('shift_date', 'desc')
            ->orderBy('start_time')
            ->get();
    }

    public function getByDate(string $date): Collection
    {
        return EmployeeShift::with(['employee', 'branch'])
            ->where('shift_date', $date)
            ->orderBy('start_time')
            ->get();
    }

    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return EmployeeShift::with(['employee', 'branch'])
            ->whereBetween('shift_date', [$startDate, $endDate])
            ->orderBy('shift_date')
            ->orderBy('start_time')
            ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return EmployeeShift::with(['employee', 'branch'])
            ->where('status', $status)
            ->orderBy('shift_date', 'desc')
            ->orderBy('start_time')
            ->get();
    }

    public function getEmployeeShiftsForDate(string $employeeId, string $date): Collection
    {
        return EmployeeShift::with('branch')
            ->where('employee_id', $employeeId)
            ->where('shift_date', $date)
            ->orderBy('start_time')
            ->get();
    }

    public function getUpcomingShifts(string $employeeId, int $days = 7): Collection
    {
        $endDate = Carbon::now()->addDays($days);

        return EmployeeShift::with('branch')
            ->where('employee_id', $employeeId)
            ->where('shift_date', '>=', Carbon::now()->toDateString())
            ->where('shift_date', '<=', $endDate->toDateString())
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->orderBy('shift_date')
            ->orderBy('start_time')
            ->get();
    }
}
