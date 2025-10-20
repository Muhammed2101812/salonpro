<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Employee;
use App\Repositories\Eloquent\EmployeeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EmployeeRepository(new Employee());
    }

    public function test_can_get_all_records(): void
    {
        Employee::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = Employee::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertDatabaseHas('employees', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $employee = Employee::factory()->create();

        $result = $this->repository->find($employee->id);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertEquals($employee->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $employee = Employee::factory()->create();
        $updateData = Employee::factory()->make()->toArray();

        $result = $this->repository->update($employee->id, $updateData);

        $this->assertInstanceOf(Employee::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $employee = Employee::factory()->create();

        $result = $this->repository->delete($employee->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('employees', ['id' => $employee->id]);
    }
}
