<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\EmployeeAttendance;
use App\Repositories\Eloquent\EmployeeAttendanceRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeAttendanceRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeAttendanceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EmployeeAttendanceRepository(new EmployeeAttendance());
    }

    public function test_can_get_all_records(): void
    {
        EmployeeAttendance::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = EmployeeAttendance::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(EmployeeAttendance::class, $result);
        $this->assertDatabaseHas('employeeAttendances', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $employeeAttendance = EmployeeAttendance::factory()->create();

        $result = $this->repository->find($employeeAttendance->id);

        $this->assertInstanceOf(EmployeeAttendance::class, $result);
        $this->assertEquals($employeeAttendance->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $employeeAttendance = EmployeeAttendance::factory()->create();
        $updateData = EmployeeAttendance::factory()->make()->toArray();

        $result = $this->repository->update($employeeAttendance->id, $updateData);

        $this->assertInstanceOf(EmployeeAttendance::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $employeeAttendance = EmployeeAttendance::factory()->create();

        $result = $this->repository->delete($employeeAttendance->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('employeeAttendances', ['id' => $employeeAttendance->id]);
    }
}
