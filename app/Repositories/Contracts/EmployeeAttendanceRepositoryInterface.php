<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\EmployeeAttendance;
use Illuminate\Support\Collection;

interface EmployeeAttendanceRepositoryInterface
{
    public function all(): Collection;
    public function find(string $id): ?EmployeeAttendance;
    public function create(array $data): EmployeeAttendance;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function getByEmployee(string $employeeId): Collection;
    public function getByBranch(string $branchId): Collection;
    public function getByDate(string $date): Collection;
    public function getByDateRange(string $startDate, string $endDate): Collection;
    public function getByStatus(string $status): Collection;
    public function getEmployeeAttendanceForDate(string $employeeId, string $date): ?EmployeeAttendance;
    public function checkIn(string $employeeId, string $branchId, string $date, string $time): EmployeeAttendance;
    public function checkOut(string $id, string $time): bool;
    public function getAttendanceStats(string $employeeId, string $startDate = null, string $endDate = null): array;
}
