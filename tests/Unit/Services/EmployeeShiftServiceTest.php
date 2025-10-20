<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\EmployeeShift;
use App\Repositories\Contracts\EmployeeShiftRepositoryInterface;
use App\Services\EmployeeShiftService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeShiftServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeShiftService $service;
    protected EmployeeShiftRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(EmployeeShiftRepositoryInterface::class);
        $this->service = new EmployeeShiftService($this->repository);
    }

    public function test_can_get_all_employeeShifts(): void
    {
        EmployeeShift::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_employeeShifts(): void
    {
        EmployeeShift::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_employeeShift(): void
    {
        $data = EmployeeShift::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(EmployeeShift::class, $result);
        $this->assertDatabaseHas('employeeShifts', ['id' => $result->id]);
    }

    public function test_can_update_employeeShift(): void
    {
        $employeeShift = EmployeeShift::factory()->create();
        $updateData = EmployeeShift::factory()->make()->toArray();

        $result = $this->service->update($employeeShift->id, $updateData);

        $this->assertInstanceOf(EmployeeShift::class, $result);
    }

    public function test_can_delete_employeeShift(): void
    {
        $employeeShift = EmployeeShift::factory()->create();

        $result = $this->service->delete($employeeShift->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('employeeShifts', ['id' => $employeeShift->id]);
    }

    public function test_can_find_employeeShift_by_id(): void
    {
        $employeeShift = EmployeeShift::factory()->create();

        $result = $this->service->findById($employeeShift->id);

        $this->assertInstanceOf(EmployeeShift::class, $result);
        $this->assertEquals($employeeShift->id, $result->id);
    }
}
