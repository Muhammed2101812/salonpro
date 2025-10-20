<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmployeeSkill;
use App\Repositories\Contracts\EmployeeSkillRepositoryInterface;
use Illuminate\Support\Collection;

class EmployeeSkillService
{
    public function __construct(
        private EmployeeSkillRepositoryInterface $repository
    ) {}

    /**
     * Get all skills
     */
    public function getAllSkills(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get skills for a specific employee
     */
    public function getEmployeeSkills(string $employeeId): Collection
    {
        return $this->repository->getByEmployee($employeeId);
    }

    /**
     * Get skills by proficiency level
     */
    public function getSkillsByProficiency(string $proficiency): Collection
    {
        return $this->repository->getByProficiency($proficiency);
    }

    /**
     * Search skills by name
     */
    public function searchSkills(string $skillName): Collection
    {
        return $this->repository->getBySkillName($skillName);
    }

    /**
     * Create a new skill for employee
     */
    public function createSkill(array $data): EmployeeSkill
    {
        // Validate proficiency
        $this->validateProficiency($data['proficiency'] ?? '');

        return $this->repository->create($data);
    }

    /**
     * Update employee skill
     */
    public function updateSkill(string $id, array $data): bool
    {
        // Validate proficiency if provided
        if (isset($data['proficiency'])) {
            $this->validateProficiency($data['proficiency']);
        }

        return $this->repository->update($id, $data);
    }

    /**
     * Delete employee skill
     */
    public function deleteSkill(string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Find skill by ID
     */
    public function findSkill(string $id): ?EmployeeSkill
    {
        return $this->repository->find($id);
    }

    /**
     * Get expert level employees for a skill
     */
    public function getExpertsForSkill(string $skillName): Collection
    {
        return $this->repository->getBySkillName($skillName)
            ->filter(fn($skill) => $skill->proficiency === 'expert');
    }

    /**
     * Get most experienced employees for a skill
     */
    public function getMostExperienced(string $skillName, int $limit = 5): Collection
    {
        return $this->repository->getBySkillName($skillName)
            ->sortByDesc('years_of_experience')
            ->take($limit);
    }

    /**
     * Check if employee has skill
     */
    public function employeeHasSkill(string $employeeId, string $skillName): bool
    {
        return $this->repository->getByEmployee($employeeId)
            ->contains('skill_name', $skillName);
    }

    /**
     * Validate proficiency level
     */
    private function validateProficiency(string $proficiency): void
    {
        $validLevels = ['beginner', 'intermediate', 'advanced', 'expert'];

        if (!in_array($proficiency, $validLevels)) {
            throw new \InvalidArgumentException(
                'Geçerli yetenek seviyeleri: beginner, intermediate, advanced, expert'
            );
        }
    }
}
