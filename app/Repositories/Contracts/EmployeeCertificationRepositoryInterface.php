<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\EmployeeCertification;
use Illuminate\Support\Collection;

interface EmployeeCertificationRepositoryInterface
{
    public function all(): Collection;
    public function find(string $id): ?EmployeeCertification;
    public function create(array $data): EmployeeCertification;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function getByEmployee(string $employeeId): Collection;
    public function getExpiringSoon(int $days = 30): Collection;
    public function getExpired(): Collection;
    public function getActive(): Collection;
}
