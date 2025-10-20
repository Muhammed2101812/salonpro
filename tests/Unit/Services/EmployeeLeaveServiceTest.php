<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\EmployeeLeave;
use App\Repositories\Contracts\EmployeeLeaveRepositoryInterface;
use App\Services\EmployeeLeaveService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeLeaveServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeLeaveService $service;
    protected EmployeeLeaveRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(EmployeeLeaveRepositoryInterface::class);
        $this->service = new EmployeeLeaveService($this->repository);
    }

    public function test_can_get_all_employeeLeaves(): void
    {
        EmployeeLeave::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_employeeLeaves(): void
    {
        EmployeeLeave::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_employeeLeave(): void
    {
        $data = EmployeeLeave::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(EmployeeLeave::class, $result);
        $this->assertDatabaseHas('employeeLeaves', ['id' => $result->id]);
    }

    public function test_can_update_employeeLeave(): void
    {
        $employeeLeave = EmployeeLeave::factory()->create();
        $updateData = EmployeeLeave::factory()->make()->toArray();

        $result = $this->service->update($employeeLeave->id, $updateData);

        $this->assertInstanceOf(EmployeeLeave::class, $result);
    }

    public function test_can_delete_employeeLeave(): void
    {
        $employeeLeave = EmployeeLeave::factory()->create();

        $result = $this->service->delete($employeeLeave->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('employeeLeaves', ['id' => $employeeLeave->id]);
    }

    public function test_can_find_employeeLeave_by_id(): void
    {
        $employeeLeave = EmployeeLeave::factory()->create();

        $result = $this->service->findById($employeeLeave->id);

        $this->assertInstanceOf(EmployeeLeave::class, $result);
        $this->assertEquals($employeeLeave->id, $result->id);
    }
}
