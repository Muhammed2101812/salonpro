<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Employee;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Services\EmployeeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeService $service;
    protected EmployeeRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(EmployeeRepositoryInterface::class);
        $this->service = new EmployeeService($this->repository);
    }

    public function test_can_get_all_employees(): void
    {
        Employee::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_employees(): void
    {
        Employee::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_employee(): void
    {
        $data = Employee::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertDatabaseHas('employees', ['id' => $result->id]);
    }

    public function test_can_update_employee(): void
    {
        $employee = Employee::factory()->create();
        $updateData = Employee::factory()->make()->toArray();

        $result = $this->service->update($employee->id, $updateData);

        $this->assertInstanceOf(Employee::class, $result);
    }

    public function test_can_delete_employee(): void
    {
        $employee = Employee::factory()->create();

        $result = $this->service->delete($employee->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('employees', ['id' => $employee->id]);
    }

    public function test_can_find_employee_by_id(): void
    {
        $employee = Employee::factory()->create();

        $result = $this->service->findById($employee->id);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertEquals($employee->id, $result->id);
    }
}
