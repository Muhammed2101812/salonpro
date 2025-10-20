<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\EmployeePerformance;
use Illuminate\Support\Collection;

interface EmployeePerformanceRepositoryInterface
{
    public function all(): Collection;
    public function find(string $id): ?EmployeePerformance;
    public function create(array $data): EmployeePerformance;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function getByEmployee(string $employeeId): Collection;
    public function getByDateRange(string $startDate, string $endDate): Collection;
    public function getLatestEvaluation(string $employeeId): ?EmployeePerformance;
    public function getTopPerformers(int $limit = 10): Collection;
    public function getAverageScores(string $employeeId): array;
}
