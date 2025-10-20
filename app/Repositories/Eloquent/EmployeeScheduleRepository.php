<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\EmployeeSchedule;
use App\Repositories\Contracts\EmployeeScheduleRepositoryInterface;
use Illuminate\Support\Collection;

class EmployeeScheduleRepository implements EmployeeScheduleRepositoryInterface
{
    public function all(): Collection
    {
        return EmployeeSchedule::with(['employee', 'branch'])->get();
    }

    public function find(string $id): ?EmployeeSchedule
    {
        return EmployeeSchedule::with(['employee', 'branch'])->find($id);
    }

    public function create(array $data): EmployeeSchedule
    {
        return EmployeeSchedule::create($data);
    }

    public function update(string $id, array $data): bool
    {
        return EmployeeSchedule::where('id', $id)->update($data);
    }

    public function delete(string $id): bool
    {
        return EmployeeSchedule::where('id', $id)->delete();
    }

    public function getByEmployee(string $employeeId): Collection
    {
        return EmployeeSchedule::with('branch')
            ->where('employee_id', $employeeId)
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
            ->get();
    }

    public function getByBranch(string $branchId): Collection
    {
        return EmployeeSchedule::with('employee')
            ->where('branch_id', $branchId)
            ->where('is_active', true)
            ->orderBy('employee_id')
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
            ->get();
    }

    public function getByDayOfWeek(string $dayOfWeek): Collection
    {
        return EmployeeSchedule::with(['employee', 'branch'])
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();
    }

    public function getActiveSchedules(string $employeeId): Collection
    {
        return EmployeeSchedule::with('branch')
            ->where('employee_id', $employeeId)
            ->where('is_active', true)
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
            ->get();
    }

    public function getEmployeeScheduleForDay(string $employeeId, string $dayOfWeek): ?EmployeeSchedule
    {
        return EmployeeSchedule::where('employee_id', $employeeId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();
    }
}
