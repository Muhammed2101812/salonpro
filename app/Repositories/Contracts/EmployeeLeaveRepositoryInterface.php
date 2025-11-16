<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EmployeeLeaveRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find leaves by employee.
     */
    public function findByEmployee(string $employeeId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Find leaves by status.
     */
    public function findByStatus(string $status, ?string $employeeId = null): Collection;

    /**
     * Find pending leaves.
     */
    public function findPending(?string $employeeId = null): Collection;

    /**
     * Find leaves by date range.
     */
    public function findByDateRange(string $startDate, string $endDate, ?string $employeeId = null): Collection;

    /**
     * Find overlapping leaves.
     */
    public function findOverlapping(string $employeeId, string $startDate, string $endDate): Collection;

    /**
     * Get leave summary.
     */
    public function getSummary(string $employeeId, string $year): array;
}
