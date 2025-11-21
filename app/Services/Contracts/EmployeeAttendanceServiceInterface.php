<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface EmployeeAttendanceServiceInterface extends BaseServiceInterface
{
    /**
     * Clock in employee.
     */
    public function clockIn(array $data): mixed;

    /**
     * Clock out employee.
     */
    public function clockOut(string $id, array $data = []): mixed;

    /**
     * Start break.
     */
    public function startBreak(string $id): mixed;

    /**
     * End break.
     */
    public function endBreak(string $id): mixed;

    /**
     * Get employee attendance.
     */
    public function getByEmployee(string $employeeId, int $perPage = 15): mixed;

    /**
     * Get today's attendance.
     */
    public function getToday(?string $branchId = null): mixed;

    /**
     * Get active (clocked in) employees.
     */
    public function getActive(?string $branchId = null): mixed;

    /**
     * Get attendance summary.
     */
    public function getSummary(string $employeeId, string $startDate, string $endDate): array;
}
