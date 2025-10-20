<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\EmployeeSkill;
use App\Repositories\Eloquent\EmployeeSkillRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeSkillRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeSkillRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EmployeeSkillRepository(new EmployeeSkill());
    }

    public function test_can_get_all_records(): void
    {
        EmployeeSkill::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = EmployeeSkill::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(EmployeeSkill::class, $result);
        $this->assertDatabaseHas('employeeSkills', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $employeeSkill = EmployeeSkill::factory()->create();

        $result = $this->repository->find($employeeSkill->id);

        $this->assertInstanceOf(EmployeeSkill::class, $result);
        $this->assertEquals($employeeSkill->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $employeeSkill = EmployeeSkill::factory()->create();
        $updateData = EmployeeSkill::factory()->make()->toArray();

        $result = $this->repository->update($employeeSkill->id, $updateData);

        $this->assertInstanceOf(EmployeeSkill::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $employeeSkill = EmployeeSkill::factory()->create();

        $result = $this->repository->delete($employeeSkill->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('employeeSkills', ['id' => $employeeSkill->id]);
    }
}
