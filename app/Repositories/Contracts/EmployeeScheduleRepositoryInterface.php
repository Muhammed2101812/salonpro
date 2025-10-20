<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\EmployeeSchedule;
use Illuminate\Support\Collection;

interface EmployeeScheduleRepositoryInterface
{
    public function all(): Collection;
    public function find(string $id): ?EmployeeSchedule;
    public function create(array $data): EmployeeSchedule;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function getByEmployee(string $employeeId): Collection;
    public function getByBranch(string $branchId): Collection;
    public function getByDayOfWeek(string $dayOfWeek): Collection;
    public function getActiveSchedules(string $employeeId): Collection;
    public function getEmployeeScheduleForDay(string $employeeId, string $dayOfWeek): ?EmployeeSchedule;
}
