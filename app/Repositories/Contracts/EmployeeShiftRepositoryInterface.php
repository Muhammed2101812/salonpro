<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\EmployeeShift;
use Illuminate\Support\Collection;

interface EmployeeShiftRepositoryInterface
{
    public function all(): Collection;
    public function find(string $id): ?EmployeeShift;
    public function create(array $data): EmployeeShift;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function getByEmployee(string $employeeId): Collection;
    public function getByBranch(string $branchId): Collection;
    public function getByDate(string $date): Collection;
    public function getByDateRange(string $startDate, string $endDate): Collection;
    public function getByStatus(string $status): Collection;
    public function getEmployeeShiftsForDate(string $employeeId, string $date): Collection;
    public function getUpcomingShifts(string $employeeId, int $days = 7): Collection;
}
