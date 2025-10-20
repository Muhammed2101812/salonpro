<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmployeeSchedule;
use Illuminate\Support\Collection;

class EmployeeScheduleService
{
    /**
     * Get all schedules
     */
    public function getAllSchedules(): Collection
    {
        return EmployeeSchedule::with(['employee', 'branch'])->get();
    }

    /**
     * Get schedules for a specific employee
     */
    public function getEmployeeSchedules(string $employeeId): Collection
    {
        return EmployeeSchedule::where('employee_id', $employeeId)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->get();
    }

    /**
     * Get schedules for a specific branch
     */
    public function getBranchSchedules(string $branchId): Collection
    {
        return EmployeeSchedule::with('employee')
            ->where('branch_id', $branchId)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Get schedules by day of week
     */
    public function getSchedulesByDay(int $dayOfWeek, ?string $branchId = null): Collection
    {
        $query = EmployeeSchedule::with('employee')
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('start_time')->get();
    }

    /**
     * Create a new schedule
     */
    public function createSchedule(array $data): EmployeeSchedule
    {
        // Validate day of week
        $this->validateDayOfWeek($data['day_of_week'] ?? -1);

        // Validate time format
        $this->validateTimes($data['start_time'], $data['end_time']);

        // Check for conflicts
        $this->checkScheduleConflict($data['employee_id'], $data['day_of_week'], $data['start_time'], $data['end_time']);

        return EmployeeSchedule::create($data);
    }

    /**
     * Update schedule
     */
    public function updateSchedule(string $id, array $data): bool
    {
        $schedule = EmployeeSchedule::find($id);

        if (!$schedule) {
            throw new \Exception('Çalışma programı bulunamadı.');
        }

        // Validate day of week if provided
        if (isset($data['day_of_week'])) {
            $this->validateDayOfWeek($data['day_of_week']);
        }

        // Validate times if both are provided
        if (isset($data['start_time']) && isset($data['end_time'])) {
            $this->validateTimes($data['start_time'], $data['end_time']);

            // Check for conflicts (excluding current schedule)
            $this->checkScheduleConflict(
                $schedule->employee_id,
                $data['day_of_week'] ?? $schedule->day_of_week,
                $data['start_time'],
                $data['end_time'],
                $id
            );
        }

        return $schedule->update($data);
    }

    /**
     * Delete schedule
     */
    public function deleteSchedule(string $id): bool
    {
        return EmployeeSchedule::where('id', $id)->delete();
    }

    /**
     * Activate/Deactivate schedule
     */
    public function toggleScheduleStatus(string $id): bool
    {
        $schedule = EmployeeSchedule::find($id);

        if (!$schedule) {
            throw new \Exception('Çalışma programı bulunamadı.');
        }

        return $schedule->update(['is_active' => !$schedule->is_active]);
    }

    /**
     * Get employee's working hours for a specific day
     */
    public function getEmployeeWorkingHours(string $employeeId, int $dayOfWeek): ?EmployeeSchedule
    {
        return EmployeeSchedule::where('employee_id', $employeeId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Check if employee is available on a specific day and time
     */
    public function isEmployeeAvailable(string $employeeId, int $dayOfWeek, string $time): bool
    {
        $schedule = $this->getEmployeeWorkingHours($employeeId, $dayOfWeek);

        if (!$schedule) {
            return false;
        }

        return $time >= $schedule->start_time && $time <= $schedule->end_time;
    }

    /**
     * Get weekly schedule for employee
     */
    public function getWeeklySchedule(string $employeeId): Collection
    {
        return EmployeeSchedule::where('employee_id', $employeeId)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');
    }

    /**
     * Bulk create schedules for employee
     */
    public function bulkCreateSchedules(string $employeeId, string $branchId, array $schedules): Collection
    {
        $created = collect();

        foreach ($schedules as $schedule) {
            $schedule['employee_id'] = $employeeId;
            $schedule['branch_id'] = $branchId;

            $created->push($this->createSchedule($schedule));
        }

        return $created;
    }

    /**
     * Validate day of week
     */
    private function validateDayOfWeek(int $day): void
    {
        if ($day < 0 || $day > 6) {
            throw new \InvalidArgumentException(
                'Geçerli günler: 0 (Pazar) - 6 (Cumartesi)'
            );
        }
    }

    /**
     * Validate times
     */
    private function validateTimes(string $startTime, string $endTime): void
    {
        if ($startTime >= $endTime) {
            throw new \InvalidArgumentException(
                'Bitiş zamanı başlangıç zamanından sonra olmalıdır.'
            );
        }
    }

    /**
     * Check for schedule conflicts
     */
    private function checkScheduleConflict(
        string $employeeId,
        int $dayOfWeek,
        string $startTime,
        string $endTime,
        ?string $excludeId = null
    ): void {
        $query = EmployeeSchedule::where('employee_id', $employeeId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw new \Exception(
                'Bu çalışan için belirlenen gün ve saatlerde çakışan bir program var.'
            );
        }
    }
}
