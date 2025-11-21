<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EmployeeCommissionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find commissions by employee.
     */
    public function findByEmployee(string $employeeId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Find commissions by payment status.
     */
    public function findByStatus(string $status, ?string $employeeId = null): Collection;

    /**
     * Find unpaid commissions.
     */
    public function findUnpaid(?string $employeeId = null): Collection;

    /**
     * Find commissions by date range.
     */
    public function findByDateRange(string $startDate, string $endDate, ?string $employeeId = null): Collection;

    /**
     * Get commission summary.
     */
    public function getSummary(string $employeeId, string $startDate, string $endDate): array;

    /**
     * Get total unpaid amount.
     */
    public function getTotalUnpaid(?string $employeeId = null): float;
}
