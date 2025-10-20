<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\EmployeeCommission;
use Illuminate\Support\Collection;

interface EmployeeCommissionRepositoryInterface
{
    public function all(): Collection;
    public function find(string $id): ?EmployeeCommission;
    public function create(array $data): EmployeeCommission;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function getByEmployee(string $employeeId): Collection;
    public function getUnpaid(string $employeeId = null): Collection;
    public function getPaid(string $employeeId = null): Collection;
    public function getByDateRange(string $startDate, string $endDate): Collection;
    public function getTotalCommissionForEmployee(string $employeeId, string $startDate = null, string $endDate = null): float;
    public function markAsPaid(array $commissionIds, string $paidDate): bool;
}
