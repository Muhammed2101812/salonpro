<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\EmployeeSkill;
use Illuminate\Support\Collection;

interface EmployeeSkillRepositoryInterface
{
    public function all(): Collection;
    public function find(string $id): ?EmployeeSkill;
    public function create(array $data): EmployeeSkill;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function getByEmployee(string $employeeId): Collection;
    public function getByProficiency(string $proficiency): Collection;
    public function getBySkillName(string $skillName): Collection;
}
