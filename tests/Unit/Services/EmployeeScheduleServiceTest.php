<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\EmployeeSchedule;
use App\Repositories\Contracts\EmployeeScheduleRepositoryInterface;
use App\Services\EmployeeScheduleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeScheduleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeScheduleService $service;
    protected EmployeeScheduleRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(EmployeeScheduleRepositoryInterface::class);
        $this->service = new EmployeeScheduleService($this->repository);
    }

    public function test_can_get_all_employeeSchedules(): void
    {
        EmployeeSchedule::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_employeeSchedules(): void
    {
        EmployeeSchedule::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_employeeSchedule(): void
    {
        $data = EmployeeSchedule::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(EmployeeSchedule::class, $result);
        $this->assertDatabaseHas('employeeSchedules', ['id' => $result->id]);
    }

    public function test_can_update_employeeSchedule(): void
    {
        $employeeSchedule = EmployeeSchedule::factory()->create();
        $updateData = EmployeeSchedule::factory()->make()->toArray();

        $result = $this->service->update($employeeSchedule->id, $updateData);

        $this->assertInstanceOf(EmployeeSchedule::class, $result);
    }

    public function test_can_delete_employeeSchedule(): void
    {
        $employeeSchedule = EmployeeSchedule::factory()->create();

        $result = $this->service->delete($employeeSchedule->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('employeeSchedules', ['id' => $employeeSchedule->id]);
    }

    public function test_can_find_employeeSchedule_by_id(): void
    {
        $employeeSchedule = EmployeeSchedule::factory()->create();

        $result = $this->service->findById($employeeSchedule->id);

        $this->assertInstanceOf(EmployeeSchedule::class, $result);
        $this->assertEquals($employeeSchedule->id, $result->id);
    }
}
