<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\EmployeeAttendanceRepositoryInterface;
use App\Services\Contracts\EmployeeAttendanceServiceInterface;
use Illuminate\Support\Facades\DB;

class EmployeeAttendanceService extends BaseService implements EmployeeAttendanceServiceInterface
{
    public function __construct(
        protected EmployeeAttendanceRepositoryInterface $attendanceRepository
    ) {
        parent::__construct($attendanceRepository);
    }

    public function clockIn(array $data): mixed
    {
        return DB::transaction(function () use ($data) {
            // Check if employee already clocked in today
            $existing = $this->attendanceRepository->model
                ->where('employee_id', $data['employee_id'])
                ->whereDate('clock_in', now()->toDateString())
                ->whereNull('clock_out')
                ->first();

            if ($existing) {
                throw new \RuntimeException('Employee already clocked in');
            }

            $data['clock_in'] = now();
            $data['status'] = $data['status'] ?? 'present';

            return $this->attendanceRepository->create($data);
        });
    }

    public function clockOut(string $id, array $data = []): mixed
    {
        return DB::transaction(function () use ($id, $data) {
            $attendance = $this->attendanceRepository->findOrFail($id);

            if ($attendance->clock_out) {
                throw new \RuntimeException('Employee already clocked out');
            }

            $clockOut = now();
            $clockIn = $attendance->clock_in;

            // Calculate total hours
            $totalHours = $clockOut->diffInMinutes($clockIn) / 60;

            // Subtract break time if exists
            if ($attendance->break_start && $attendance->break_end) {
                $breakMinutes = $attendance->break_end->diffInMinutes($attendance->break_start);
                $totalHours -= ($breakMinutes / 60);
            }

            return $this->attendanceRepository->update($id, [
                'clock_out' => $clockOut,
                'total_hours' => round($totalHours, 2),
                'notes' => $data['notes'] ?? $attendance->notes,
            ]);
        });
    }

    public function startBreak(string $id): mixed
    {
        return DB::transaction(function () use ($id) {
            $attendance = $this->attendanceRepository->findOrFail($id);

            if ($attendance->break_start) {
                throw new \RuntimeException('Break already started');
            }

            if ($attendance->clock_out) {
                throw new \RuntimeException('Cannot start break after clocking out');
            }

            return $this->attendanceRepository->update($id, [
                'break_start' => now(),
            ]);
        });
    }

    public function endBreak(string $id): mixed
    {
        return DB::transaction(function () use ($id) {
            $attendance = $this->attendanceRepository->findOrFail($id);

            if (!$attendance->break_start) {
                throw new \RuntimeException('Break not started');
            }

            if ($attendance->break_end) {
                throw new \RuntimeException('Break already ended');
            }

            return $this->attendanceRepository->update($id, [
                'break_end' => now(),
            ]);
        });
    }

    public function getByEmployee(string $employeeId, int $perPage = 15): mixed
    {
        return $this->attendanceRepository->findByEmployee($employeeId, $perPage);
    }

    public function getToday(?string $branchId = null): mixed
    {
        return $this->attendanceRepository->findToday($branchId);
    }

    public function getActive(?string $branchId = null): mixed
    {
        return $this->attendanceRepository->findActive($branchId);
    }

    public function getSummary(string $employeeId, string $startDate, string $endDate): array
    {
        return $this->attendanceRepository->getSummary($employeeId, $startDate, $endDate);
    }
}
