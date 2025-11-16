<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface EmployeeCommissionServiceInterface extends BaseServiceInterface
{
    /**
     * Get employee commissions.
     */
    public function getByEmployee(string $employeeId, int $perPage = 15): mixed;

    /**
     * Get unpaid commissions.
     */
    public function getUnpaid(?string $employeeId = null): mixed;

    /**
     * Mark commission as paid.
     */
    public function markAsPaid(string $id): mixed;

    /**
     * Mark multiple commissions as paid.
     */
    public function markMultipleAsPaid(array $ids): array;

    /**
     * Get commission summary.
     */
    public function getSummary(string $employeeId, string $startDate, string $endDate): array;

    /**
     * Calculate commission.
     */
    public function calculateCommission(float $baseAmount, float $commissionRate): float;
}
