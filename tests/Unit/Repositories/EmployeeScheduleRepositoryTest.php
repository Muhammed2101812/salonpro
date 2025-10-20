<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\EmployeeSchedule;
use App\Repositories\Eloquent\EmployeeScheduleRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeScheduleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeScheduleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EmployeeScheduleRepository(new EmployeeSchedule());
    }

    public function test_can_get_all_records(): void
    {
        EmployeeSchedule::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = EmployeeSchedule::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(EmployeeSchedule::class, $result);
        $this->assertDatabaseHas('employeeSchedules', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $employeeSchedule = EmployeeSchedule::factory()->create();

        $result = $this->repository->find($employeeSchedule->id);

        $this->assertInstanceOf(EmployeeSchedule::class, $result);
        $this->assertEquals($employeeSchedule->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $employeeSchedule = EmployeeSchedule::factory()->create();
        $updateData = EmployeeSchedule::factory()->make()->toArray();

        $result = $this->repository->update($employeeSchedule->id, $updateData);

        $this->assertInstanceOf(EmployeeSchedule::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $employeeSchedule = EmployeeSchedule::factory()->create();

        $result = $this->repository->delete($employeeSchedule->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('employeeSchedules', ['id' => $employeeSchedule->id]);
    }
}
