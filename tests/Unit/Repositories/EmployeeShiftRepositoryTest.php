<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\EmployeeShift;
use App\Repositories\Eloquent\EmployeeShiftRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeShiftRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeShiftRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EmployeeShiftRepository(new EmployeeShift());
    }

    public function test_can_get_all_records(): void
    {
        EmployeeShift::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = EmployeeShift::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(EmployeeShift::class, $result);
        $this->assertDatabaseHas('employeeShifts', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $employeeShift = EmployeeShift::factory()->create();

        $result = $this->repository->find($employeeShift->id);

        $this->assertInstanceOf(EmployeeShift::class, $result);
        $this->assertEquals($employeeShift->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $employeeShift = EmployeeShift::factory()->create();
        $updateData = EmployeeShift::factory()->make()->toArray();

        $result = $this->repository->update($employeeShift->id, $updateData);

        $this->assertInstanceOf(EmployeeShift::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $employeeShift = EmployeeShift::factory()->create();

        $result = $this->repository->delete($employeeShift->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('employeeShifts', ['id' => $employeeShift->id]);
    }
}
