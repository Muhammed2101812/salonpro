<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EmployeeAttendanceRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find attendance by employee.
     */
    public function findByEmployee(string $employeeId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Find attendance by branch.
     */
    public function findByBranch(string $branchId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Find attendance by date range.
     */
    public function findByDateRange(string $startDate, string $endDate, ?string $employeeId = null): Collection;

    /**
     * Find today's attendance.
     */
    public function findToday(?string $branchId = null): Collection;

    /**
     * Find active (clocked in) employees.
     */
    public function findActive(?string $branchId = null): Collection;

    /**
     * Get attendance summary for employee.
     */
    public function getSummary(string $employeeId, string $startDate, string $endDate): array;
}
