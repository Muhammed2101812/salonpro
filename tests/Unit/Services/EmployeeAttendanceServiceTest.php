<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\EmployeeAttendance;
use App\Repositories\Contracts\EmployeeAttendanceRepositoryInterface;
use App\Services\EmployeeAttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeAttendanceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeAttendanceService $service;
    protected EmployeeAttendanceRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(EmployeeAttendanceRepositoryInterface::class);
        $this->service = new EmployeeAttendanceService($this->repository);
    }

    public function test_can_get_all_employeeAttendances(): void
    {
        EmployeeAttendance::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_employeeAttendances(): void
    {
        EmployeeAttendance::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_employeeAttendance(): void
    {
        $data = EmployeeAttendance::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(EmployeeAttendance::class, $result);
        $this->assertDatabaseHas('employeeAttendances', ['id' => $result->id]);
    }

    public function test_can_update_employeeAttendance(): void
    {
        $employeeAttendance = EmployeeAttendance::factory()->create();
        $updateData = EmployeeAttendance::factory()->make()->toArray();

        $result = $this->service->update($employeeAttendance->id, $updateData);

        $this->assertInstanceOf(EmployeeAttendance::class, $result);
    }

    public function test_can_delete_employeeAttendance(): void
    {
        $employeeAttendance = EmployeeAttendance::factory()->create();

        $result = $this->service->delete($employeeAttendance->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('employeeAttendances', ['id' => $employeeAttendance->id]);
    }

    public function test_can_find_employeeAttendance_by_id(): void
    {
        $employeeAttendance = EmployeeAttendance::factory()->create();

        $result = $this->service->findById($employeeAttendance->id);

        $this->assertInstanceOf(EmployeeAttendance::class, $result);
        $this->assertEquals($employeeAttendance->id, $result->id);
    }
}
