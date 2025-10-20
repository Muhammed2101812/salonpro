<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmployeeAttendance;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class EmployeeAttendanceService
{
    /**
     * Get all attendance records
     */
    public function getAllAttendances(): Collection
    {
        return EmployeeAttendance::with(['employee', 'branch'])
            ->orderBy('attendance_date', 'desc')
            ->get();
    }

    /**
     * Get attendance records for a specific employee
     */
    public function getEmployeeAttendances(string $employeeId, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = EmployeeAttendance::where('employee_id', $employeeId);

        if ($startDate) {
            $query->whereDate('attendance_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('attendance_date', '<=', $endDate);
        }

        return $query->orderBy('attendance_date', 'desc')->get();
    }

    /**
     * Get attendance records for a specific branch
     */
    public function getBranchAttendances(string $branchId, ?Carbon $date = null): Collection
    {
        $query = EmployeeAttendance::with('employee')
            ->where('branch_id', $branchId);

        if ($date) {
            $query->whereDate('attendance_date', $date);
        }

        return $query->orderBy('attendance_date', 'desc')
            ->orderBy('check_in')
            ->get();
    }

    /**
     * Get attendance by date
     */
    public function getAttendanceByDate(Carbon $date, ?string $branchId = null): Collection
    {
        $query = EmployeeAttendance::with('employee')
            ->whereDate('attendance_date', $date);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('check_in')->get();
    }

    /**
     * Get attendance by status
     */
    public function getAttendanceByStatus(string $status, ?Carbon $date = null): Collection
    {
        $query = EmployeeAttendance::with('employee')
            ->where('status', $status);

        if ($date) {
            $query->whereDate('attendance_date', $date);
        }

        return $query->orderBy('attendance_date', 'desc')->get();
    }

    /**
     * Create a new attendance record
     */
    public function createAttendance(array $data): EmployeeAttendance
    {
        // Check if attendance already exists for the day
        $this->checkDuplicateAttendance($data['employee_id'], $data['attendance_date']);

        // Validate times if both provided
        if (isset($data['check_in']) && isset($data['check_out'])) {
            $this->validateTimes($data['check_in'], $data['check_out']);

            // Calculate total hours
            if (!isset($data['total_hours'])) {
                $data['total_hours'] = $this->calculateTotalHours($data['check_in'], $data['check_out']);
            }
        }

        // Determine status if not provided
        if (!isset($data['status'])) {
            $data['status'] = $this->determineStatus($data);
        }

        return EmployeeAttendance::create($data);
    }

    /**
     * Update attendance record
     */
    public function updateAttendance(string $id, array $data): bool
    {
        $attendance = EmployeeAttendance::find($id);

        if (!$attendance) {
            throw new \Exception('Devamsızlık kaydı bulunamadı.');
        }

        // Validate times if both are provided
        if (isset($data['check_in']) && isset($data['check_out'])) {
            $this->validateTimes($data['check_in'], $data['check_out']);
            $data['total_hours'] = $this->calculateTotalHours($data['check_in'], $data['check_out']);
        } elseif (isset($data['check_in']) && $attendance->check_out) {
            $data['total_hours'] = $this->calculateTotalHours($data['check_in'], $attendance->check_out);
        } elseif (isset($data['check_out']) && $attendance->check_in) {
            $data['total_hours'] = $this->calculateTotalHours($attendance->check_in, $data['check_out']);
        }

        return $attendance->update($data);
    }

    /**
     * Delete attendance record
     */
    public function deleteAttendance(string $id): bool
    {
        return EmployeeAttendance::where('id', $id)->delete();
    }

    /**
     * Clock in employee
     */
    public function clockIn(string $employeeId, string $branchId, ?string $notes = null): EmployeeAttendance
    {
        // Check if already clocked in today
        $existingAttendance = $this->getTodayAttendance($employeeId);

        if ($existingAttendance) {
            throw new \Exception('Bu gün için zaten giriş yapılmış.');
        }

        return $this->createAttendance([
            'employee_id' => $employeeId,
            'branch_id' => $branchId,
            'attendance_date' => now()->toDateString(),
            'check_in' => now()->format('H:i:s'),
            'status' => 'present',
            'notes' => $notes,
        ]);
    }

    /**
     * Clock out employee
     */
    public function clockOut(string $employeeId, ?string $notes = null): bool
    {
        $attendance = $this->getTodayAttendance($employeeId);

        if (!$attendance) {
            throw new \Exception('Bugün için giriş kaydı bulunamadı.');
        }

        if ($attendance->check_out) {
            throw new \Exception('Çıkış zaten yapılmış.');
        }

        $checkOut = now()->format('H:i:s');
        $totalHours = $this->calculateTotalHours($attendance->check_in, $checkOut);

        return $attendance->update([
            'check_out' => $checkOut,
            'total_hours' => $totalHours,
            'notes' => $notes ? ($attendance->notes ? $attendance->notes . ' | ' . $notes : $notes) : $attendance->notes,
        ]);
    }

    /**
     * Get today's attendance for employee
     */
    public function getTodayAttendance(string $employeeId): ?EmployeeAttendance
    {
        return EmployeeAttendance::where('employee_id', $employeeId)
            ->whereDate('attendance_date', now())
            ->first();
    }

    /**
     * Mark employee as absent
     */
    public function markAbsent(string $employeeId, string $branchId, Carbon $date, ?string $notes = null): EmployeeAttendance
    {
        // Check if attendance already exists
        $this->checkDuplicateAttendance($employeeId, $date->toDateString());

        return $this->createAttendance([
            'employee_id' => $employeeId,
            'branch_id' => $branchId,
            'attendance_date' => $date->toDateString(),
            'status' => 'absent',
            'notes' => $notes,
        ]);
    }

    /**
     * Mark employee as late
     */
    public function markLate(string $id): bool
    {
        return EmployeeAttendance::where('id', $id)
            ->update(['status' => 'late']);
    }

    /**
     * Get attendance statistics for employee
     */
    public function getEmployeeAttendanceStats(string $employeeId, Carbon $startDate, Carbon $endDate): array
    {
        $attendances = $this->getEmployeeAttendances($employeeId, $startDate, $endDate);

        $workingDays = $startDate->diffInWeekdays($endDate) + 1;

        $byStatus = $attendances->groupBy('status');

        return [
            'total_days' => $attendances->count(),
            'working_days' => $workingDays,
            'present_days' => $byStatus->get('present', collect())->count(),
            'absent_days' => $byStatus->get('absent', collect())->count(),
            'late_days' => $byStatus->get('late', collect())->count(),
            'half_day_count' => $byStatus->get('half_day', collect())->count(),
            'total_hours' => $attendances->sum('total_hours'),
            'average_hours_per_day' => $attendances->avg('total_hours'),
            'attendance_rate' => $workingDays > 0
                ? round(($byStatus->get('present', collect())->count() / $workingDays) * 100, 2)
                : 0,
        ];
    }

    /**
     * Get late arrivals
     */
    public function getLateArrivals(?Carbon $startDate = null, ?Carbon $endDate = null, ?string $branchId = null): Collection
    {
        $query = EmployeeAttendance::with('employee')
            ->where('status', 'late');

        if ($startDate) {
            $query->whereDate('attendance_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('attendance_date', '<=', $endDate);
        }

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('attendance_date', 'desc')->get();
    }

    /**
     * Get absent employees
     */
    public function getAbsentEmployees(?Carbon $date = null, ?string $branchId = null): Collection
    {
        $date = $date ?? now();

        $query = EmployeeAttendance::with('employee')
            ->where('status', 'absent')
            ->whereDate('attendance_date', $date);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    /**
     * Get attendance summary by branch
     */
    public function getBranchAttendanceSummary(string $branchId, Carbon $date): array
    {
        $attendances = $this->getBranchAttendances($branchId, $date);

        $byStatus = $attendances->groupBy('status');

        return [
            'date' => $date->toDateString(),
            'total_employees' => $attendances->count(),
            'present' => $byStatus->get('present', collect())->count(),
            'absent' => $byStatus->get('absent', collect())->count(),
            'late' => $byStatus->get('late', collect())->count(),
            'half_day' => $byStatus->get('half_day', collect())->count(),
            'total_hours' => $attendances->sum('total_hours'),
        ];
    }

    /**
     * Get monthly attendance report
     */
    public function getMonthlyAttendanceReport(string $employeeId, int $month, int $year): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $attendances = $this->getEmployeeAttendances($employeeId, $startDate, $endDate);

        $dailyReport = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $attendance = $attendances->firstWhere('attendance_date', $currentDate->toDateString());

            $dailyReport[] = [
                'date' => $currentDate->toDateString(),
                'day_name' => $currentDate->locale('tr')->dayName,
                'is_weekend' => $currentDate->isWeekend(),
                'status' => $attendance ? $attendance->status : null,
                'check_in' => $attendance ? $attendance->check_in : null,
                'check_out' => $attendance ? $attendance->check_out : null,
                'total_hours' => $attendance ? $attendance->total_hours : null,
                'notes' => $attendance ? $attendance->notes : null,
            ];

            $currentDate->addDay();
        }

        return [
            'month' => $month,
            'year' => $year,
            'employee_id' => $employeeId,
            'statistics' => $this->getEmployeeAttendanceStats($employeeId, $startDate, $endDate),
            'daily_records' => $dailyReport,
        ];
    }

    /**
     * Check if employee has clocked in today
     */
    public function hasClockedInToday(string $employeeId): bool
    {
        return EmployeeAttendance::where('employee_id', $employeeId)
            ->whereDate('attendance_date', now())
            ->whereNotNull('check_in')
            ->exists();
    }

    /**
     * Check if employee has clocked out today
     */
    public function hasClockedOutToday(string $employeeId): bool
    {
        return EmployeeAttendance::where('employee_id', $employeeId)
            ->whereDate('attendance_date', now())
            ->whereNotNull('check_out')
            ->exists();
    }

    /**
     * Validate times
     */
    private function validateTimes(string $checkIn, string $checkOut): void
    {
        if ($checkIn >= $checkOut) {
            throw new \InvalidArgumentException(
                'Çıkış zamanı giriş zamanından sonra olmalıdır.'
            );
        }
    }

    /**
     * Calculate total hours between check in and check out
     */
    private function calculateTotalHours(string $checkIn, string $checkOut): int
    {
        $start = Carbon::parse($checkIn);
        $end = Carbon::parse($checkOut);

        return (int) $start->diffInMinutes($end);
    }

    /**
     * Determine attendance status
     */
    private function determineStatus(array $data): string
    {
        // If check_in time is provided, check if it's late
        if (isset($data['check_in'])) {
            $checkInTime = Carbon::parse($data['check_in']);
            $expectedTime = Carbon::parse('09:00:00'); // Default expected time

            if ($checkInTime->greaterThan($expectedTime)) {
                return 'late';
            }

            return 'present';
        }

        return 'absent';
    }

    /**
     * Check for duplicate attendance
     */
    private function checkDuplicateAttendance(string $employeeId, string $date): void
    {
        $exists = EmployeeAttendance::where('employee_id', $employeeId)
            ->whereDate('attendance_date', $date)
            ->exists();

        if ($exists) {
            throw new \Exception(
                'Bu tarih için zaten bir devamsızlık kaydı var.'
            );
        }
    }
}
