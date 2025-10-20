<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\EmployeeLeave;
use App\Repositories\Eloquent\EmployeeLeaveRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeLeaveRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeLeaveRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EmployeeLeaveRepository(new EmployeeLeave());
    }

    public function test_can_get_all_records(): void
    {
        EmployeeLeave::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = EmployeeLeave::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(EmployeeLeave::class, $result);
        $this->assertDatabaseHas('employeeLeaves', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $employeeLeave = EmployeeLeave::factory()->create();

        $result = $this->repository->find($employeeLeave->id);

        $this->assertInstanceOf(EmployeeLeave::class, $result);
        $this->assertEquals($employeeLeave->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $employeeLeave = EmployeeLeave::factory()->create();
        $updateData = EmployeeLeave::factory()->make()->toArray();

        $result = $this->repository->update($employeeLeave->id, $updateData);

        $this->assertInstanceOf(EmployeeLeave::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $employeeLeave = EmployeeLeave::factory()->create();

        $result = $this->repository->delete($employeeLeave->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('employeeLeaves', ['id' => $employeeLeave->id]);
    }
}
