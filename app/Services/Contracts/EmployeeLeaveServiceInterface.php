<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface EmployeeLeaveServiceInterface extends BaseServiceInterface
{
    /**
     * Get employee leaves.
     */
    public function getByEmployee(string $employeeId, int $perPage = 15): mixed;

    /**
     * Get pending leaves.
     */
    public function getPending(?string $employeeId = null): mixed;

    /**
     * Request leave.
     */
    public function requestLeave(array $data): mixed;

    /**
     * Approve leave.
     */
    public function approve(string $id, string $approvedBy): mixed;

    /**
     * Reject leave.
     */
    public function reject(string $id, string $approvedBy, ?string $reason = null): mixed;

    /**
     * Cancel leave.
     */
    public function cancel(string $id, ?string $reason = null): mixed;

    /**
     * Get leave summary.
     */
    public function getSummary(string $employeeId, string $year): array;

    /**
     * Check if employee has overlapping leaves.
     */
    public function checkOverlapping(string $employeeId, string $startDate, string $endDate): bool;
}
