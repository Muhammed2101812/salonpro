<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmployeeShift;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class EmployeeShiftService
{
    /**
     * Get all shifts
     */
    public function getAllShifts(): Collection
    {
        return EmployeeShift::with(['employee', 'branch'])
            ->orderBy('shift_date', 'desc')
            ->get();
    }

    /**
     * Get shifts for a specific employee
     */
    public function getEmployeeShifts(string $employeeId, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = EmployeeShift::where('employee_id', $employeeId);

        if ($startDate) {
            $query->whereDate('shift_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('shift_date', '<=', $endDate);
        }

        return $query->orderBy('shift_date', 'desc')->get();
    }

    /**
     * Get shifts for a specific branch
     */
    public function getBranchShifts(string $branchId, ?Carbon $date = null): Collection
    {
        $query = EmployeeShift::with('employee')
            ->where('branch_id', $branchId);

        if ($date) {
            $query->whereDate('shift_date', $date);
        }

        return $query->orderBy('shift_date', 'desc')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Get shifts by date
     */
    public function getShiftsByDate(Carbon $date, ?string $branchId = null): Collection
    {
        $query = EmployeeShift::with('employee')
            ->whereDate('shift_date', $date);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('start_time')->get();
    }

    /**
     * Get shifts by status
     */
    public function getShiftsByStatus(string $status, ?string $branchId = null): Collection
    {
        $query = EmployeeShift::with(['employee', 'branch'])
            ->where('status', $status);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('shift_date', 'desc')->get();
    }

    /**
     * Create a new shift
     */
    public function createShift(array $data): EmployeeShift
    {
        // Validate times
        $this->validateTimes($data['start_time'], $data['end_time']);

        // Validate break times if provided
        if (isset($data['break_start']) && isset($data['break_end'])) {
            $this->validateBreakTimes(
                $data['start_time'],
                $data['end_time'],
                $data['break_start'],
                $data['break_end']
            );
        }

        // Check for shift conflicts
        $this->checkShiftConflict(
            $data['employee_id'],
            $data['shift_date'],
            $data['start_time'],
            $data['end_time']
        );

        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'scheduled';
        }

        return EmployeeShift::create($data);
    }

    /**
     * Update shift
     */
    public function updateShift(string $id, array $data): bool
    {
        $shift = EmployeeShift::find($id);

        if (!$shift) {
            throw new \Exception('Vardiya bulunamadı.');
        }

        // Validate times if both are provided
        if (isset($data['start_time']) && isset($data['end_time'])) {
            $this->validateTimes($data['start_time'], $data['end_time']);

            // Check for conflicts (excluding current shift)
            $this->checkShiftConflict(
                $shift->employee_id,
                $data['shift_date'] ?? $shift->shift_date,
                $data['start_time'],
                $data['end_time'],
                $id
            );
        }

        // Validate break times if provided
        if (isset($data['break_start']) && isset($data['break_end'])) {
            $this->validateBreakTimes(
                $data['start_time'] ?? $shift->start_time,
                $data['end_time'] ?? $shift->end_time,
                $data['break_start'],
                $data['break_end']
            );
        }

        return $shift->update($data);
    }

    /**
     * Delete shift
     */
    public function deleteShift(string $id): bool
    {
        $shift = EmployeeShift::find($id);

        if (!$shift) {
            throw new \Exception('Vardiya bulunamadı.');
        }

        // Only allow deletion of future or scheduled shifts
        if ($shift->status === 'completed') {
            throw new \Exception('Tamamlanmış vardiyalar silinemez.');
        }

        return $shift->delete();
    }

    /**
     * Update shift status
     */
    public function updateShiftStatus(string $id, string $status): bool
    {
        $validStatuses = ['scheduled', 'in_progress', 'completed', 'cancelled', 'no_show'];

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException('Geçersiz vardiya durumu.');
        }

        return EmployeeShift::where('id', $id)->update(['status' => $status]);
    }

    /**
     * Get upcoming shifts for employee
     */
    public function getUpcomingShifts(string $employeeId, int $days = 7): Collection
    {
        return EmployeeShift::where('employee_id', $employeeId)
            ->whereDate('shift_date', '>=', now())
            ->whereDate('shift_date', '<=', now()->addDays($days))
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->orderBy('shift_date')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Get current active shift for employee
     */
    public function getCurrentShift(string $employeeId): ?EmployeeShift
    {
        return EmployeeShift::where('employee_id', $employeeId)
            ->whereDate('shift_date', now())
            ->where('status', 'in_progress')
            ->first();
    }

    /**
     * Calculate total work hours for shift
     */
    public function calculateShiftHours(EmployeeShift $shift): float
    {
        $start = Carbon::parse($shift->shift_date . ' ' . $shift->start_time);
        $end = Carbon::parse($shift->shift_date . ' ' . $shift->end_time);

        $totalMinutes = $end->diffInMinutes($start);

        // Subtract break time if present
        if ($shift->break_start && $shift->break_end) {
            $breakStart = Carbon::parse($shift->shift_date . ' ' . $shift->break_start);
            $breakEnd = Carbon::parse($shift->shift_date . ' ' . $shift->break_end);
            $totalMinutes -= $breakEnd->diffInMinutes($breakStart);
        }

        return round($totalMinutes / 60, 2);
    }

    /**
     * Get shift statistics for employee
     */
    public function getEmployeeShiftStats(string $employeeId, Carbon $startDate, Carbon $endDate): array
    {
        $shifts = $this->getEmployeeShifts($employeeId, $startDate, $endDate);

        return [
            'total_shifts' => $shifts->count(),
            'completed_shifts' => $shifts->where('status', 'completed')->count(),
            'cancelled_shifts' => $shifts->where('status', 'cancelled')->count(),
            'no_show_count' => $shifts->where('status', 'no_show')->count(),
            'total_hours' => $shifts->sum(fn($shift) => $this->calculateShiftHours($shift)),
        ];
    }

    /**
     * Bulk create shifts
     */
    public function bulkCreateShifts(array $shiftsData): Collection
    {
        $created = collect();

        foreach ($shiftsData as $shiftData) {
            $created->push($this->createShift($shiftData));
        }

        return $created;
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
     * Validate break times
     */
    private function validateBreakTimes(string $shiftStart, string $shiftEnd, string $breakStart, string $breakEnd): void
    {
        if ($breakStart >= $breakEnd) {
            throw new \InvalidArgumentException(
                'Mola bitiş zamanı mola başlangıç zamanından sonra olmalıdır.'
            );
        }

        if ($breakStart < $shiftStart || $breakEnd > $shiftEnd) {
            throw new \InvalidArgumentException(
                'Mola zamanları vardiya saatleri içinde olmalıdır.'
            );
        }
    }

    /**
     * Check for shift conflicts
     */
    private function checkShiftConflict(
        string $employeeId,
        string $shiftDate,
        string $startTime,
        string $endTime,
        ?string $excludeId = null
    ): void {
        $query = EmployeeShift::where('employee_id', $employeeId)
            ->whereDate('shift_date', $shiftDate)
            ->whereNotIn('status', ['cancelled'])
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
                'Bu çalışan için belirlenen tarih ve saatlerde çakışan bir vardiya var.'
            );
        }
    }
}
