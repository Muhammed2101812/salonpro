<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\EmployeeSkill;
use App\Repositories\Contracts\EmployeeSkillRepositoryInterface;
use App\Services\EmployeeSkillService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeSkillServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeSkillService $service;
    protected EmployeeSkillRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(EmployeeSkillRepositoryInterface::class);
        $this->service = new EmployeeSkillService($this->repository);
    }

    public function test_can_get_all_employeeSkills(): void
    {
        EmployeeSkill::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_employeeSkills(): void
    {
        EmployeeSkill::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_employeeSkill(): void
    {
        $data = EmployeeSkill::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(EmployeeSkill::class, $result);
        $this->assertDatabaseHas('employeeSkills', ['id' => $result->id]);
    }

    public function test_can_update_employeeSkill(): void
    {
        $employeeSkill = EmployeeSkill::factory()->create();
        $updateData = EmployeeSkill::factory()->make()->toArray();

        $result = $this->service->update($employeeSkill->id, $updateData);

        $this->assertInstanceOf(EmployeeSkill::class, $result);
    }

    public function test_can_delete_employeeSkill(): void
    {
        $employeeSkill = EmployeeSkill::factory()->create();

        $result = $this->service->delete($employeeSkill->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('employeeSkills', ['id' => $employeeSkill->id]);
    }

    public function test_can_find_employeeSkill_by_id(): void
    {
        $employeeSkill = EmployeeSkill::factory()->create();

        $result = $this->service->findById($employeeSkill->id);

        $this->assertInstanceOf(EmployeeSkill::class, $result);
        $this->assertEquals($employeeSkill->id, $result->id);
    }
}
