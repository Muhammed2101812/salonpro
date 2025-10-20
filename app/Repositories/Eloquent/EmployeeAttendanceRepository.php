<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\EmployeeAttendance;
use App\Repositories\Contracts\EmployeeAttendanceRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class EmployeeAttendanceRepository implements EmployeeAttendanceRepositoryInterface
{
    public function all(): Collection
    {
        return EmployeeAttendance::with(['employee', 'branch'])->get();
    }

    public function find(string $id): ?EmployeeAttendance
    {
        return EmployeeAttendance::with(['employee', 'branch'])->find($id);
    }

    public function create(array $data): EmployeeAttendance
    {
        return EmployeeAttendance::create($data);
    }

    public function update(string $id, array $data): bool
    {
        return EmployeeAttendance::where('id', $id)->update($data);
    }

    public function delete(string $id): bool
    {
        return EmployeeAttendance::where('id', $id)->delete();
    }

    public function getByEmployee(string $employeeId): Collection
    {
        return EmployeeAttendance::with('branch')
            ->where('employee_id', $employeeId)
            ->orderBy('attendance_date', 'desc')
            ->get();
    }

    public function getByBranch(string $branchId): Collection
    {
        return EmployeeAttendance::with('employee')
            ->where('branch_id', $branchId)
            ->orderBy('attendance_date', 'desc')
            ->get();
    }

    public function getByDate(string $date): Collection
    {
        return EmployeeAttendance::with(['employee', 'branch'])
            ->where('attendance_date', $date)
            ->orderBy('check_in')
            ->get();
    }

    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return EmployeeAttendance::with(['employee', 'branch'])
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->orderBy('attendance_date', 'desc')
            ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return EmployeeAttendance::with(['employee', 'branch'])
            ->where('status', $status)
            ->orderBy('attendance_date', 'desc')
            ->get();
    }

    public function getEmployeeAttendanceForDate(string $employeeId, string $date): ?EmployeeAttendance
    {
        return EmployeeAttendance::where('employee_id', $employeeId)
            ->where('attendance_date', $date)
            ->first();
    }

    public function checkIn(string $employeeId, string $branchId, string $date, string $time): EmployeeAttendance
    {
        return EmployeeAttendance::create([
            'employee_id' => $employeeId,
            'branch_id' => $branchId,
            'attendance_date' => $date,
            'check_in' => $time,
            'status' => 'present',
        ]);
    }

    public function checkOut(string $id, string $time): bool
    {
        $attendance = EmployeeAttendance::find($id);

        if (!$attendance) {
            return false;
        }

        $checkIn = Carbon::parse($attendance->check_in);
        $checkOut = Carbon::parse($time);
        $totalHours = $checkIn->diffInHours($checkOut);

        return $attendance->update([
            'check_out' => $time,
            'total_hours' => $totalHours,
        ]);
    }

    public function getAttendanceStats(string $employeeId, string $startDate = null, string $endDate = null): array
    {
        $query = EmployeeAttendance::where('employee_id', $employeeId);

        if ($startDate && $endDate) {
            $query->whereBetween('attendance_date', [$startDate, $endDate]);
        }

        $total = $query->count();
        $present = $query->clone()->where('status', 'present')->count();
        $absent = $query->clone()->where('status', 'absent')->count();
        $late = $query->clone()->where('status', 'late')->count();
        $halfDay = $query->clone()->where('status', 'half_day')->count();
        $onLeave = $query->clone()->where('status', 'on_leave')->count();
        $totalHours = $query->clone()->sum('total_hours');

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'half_day' => $halfDay,
            'on_leave' => $onLeave,
            'total_hours' => $totalHours,
            'attendance_rate' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
        ];
    }
}
