<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\EmployeeSkill;
use App\Repositories\Contracts\EmployeeSkillRepositoryInterface;
use Illuminate\Support\Collection;

class EmployeeSkillRepository implements EmployeeSkillRepositoryInterface
{
    public function all(): Collection
    {
        return EmployeeSkill::with('employee')->get();
    }

    public function find(string $id): ?EmployeeSkill
    {
        return EmployeeSkill::with('employee')->find($id);
    }

    public function create(array $data): EmployeeSkill
    {
        return EmployeeSkill::create($data);
    }

    public function update(string $id, array $data): bool
    {
        return EmployeeSkill::where('id', $id)->update($data);
    }

    public function delete(string $id): bool
    {
        return EmployeeSkill::where('id', $id)->delete();
    }

    public function getByEmployee(string $employeeId): Collection
    {
        return EmployeeSkill::where('employee_id', $employeeId)
            ->orderBy('proficiency', 'desc')
            ->orderBy('years_of_experience', 'desc')
            ->get();
    }

    public function getByProficiency(string $proficiency): Collection
    {
        return EmployeeSkill::with('employee')
            ->where('proficiency', $proficiency)
            ->orderBy('years_of_experience', 'desc')
            ->get();
    }

    public function getBySkillName(string $skillName): Collection
    {
        return EmployeeSkill::with('employee')
            ->where('skill_name', 'like', "%{$skillName}%")
            ->orderBy('proficiency', 'desc')
            ->get();
    }
}
