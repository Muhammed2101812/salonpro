<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\EmployeeLeave;
use Illuminate\Support\Collection;

interface EmployeeLeaveRepositoryInterface
{
    public function all(): Collection;
    public function find(string $id): ?EmployeeLeave;
    public function create(array $data): EmployeeLeave;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function getByEmployee(string $employeeId): Collection;
    public function getByStatus(string $status): Collection;
    public function getByLeaveType(string $leaveType): Collection;
    public function getPending(): Collection;
    public function getByDateRange(string $startDate, string $endDate): Collection;
    public function getTotalLeaveDays(string $employeeId, string $leaveType = null, int $year = null): int;
    public function approve(string $id, string $approvedBy): bool;
    public function reject(string $id, string $approvedBy, string $reason): bool;
}
