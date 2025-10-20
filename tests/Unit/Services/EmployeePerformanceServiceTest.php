<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\EmployeePerformance;
use App\Repositories\Contracts\EmployeePerformanceRepositoryInterface;
use App\Services\EmployeePerformanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeePerformanceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeePerformanceService $service;
    protected EmployeePerformanceRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(EmployeePerformanceRepositoryInterface::class);
        $this->service = new EmployeePerformanceService($this->repository);
    }

    public function test_can_get_all_employeePerformances(): void
    {
        EmployeePerformance::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_employeePerformances(): void
    {
        EmployeePerformance::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_employeePerformance(): void
    {
        $data = EmployeePerformance::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(EmployeePerformance::class, $result);
        $this->assertDatabaseHas('employeePerformances', ['id' => $result->id]);
    }

    public function test_can_update_employeePerformance(): void
    {
        $employeePerformance = EmployeePerformance::factory()->create();
        $updateData = EmployeePerformance::factory()->make()->toArray();

        $result = $this->service->update($employeePerformance->id, $updateData);

        $this->assertInstanceOf(EmployeePerformance::class, $result);
    }

    public function test_can_delete_employeePerformance(): void
    {
        $employeePerformance = EmployeePerformance::factory()->create();

        $result = $this->service->delete($employeePerformance->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('employeePerformances', ['id' => $employeePerformance->id]);
    }

    public function test_can_find_employeePerformance_by_id(): void
    {
        $employeePerformance = EmployeePerformance::factory()->create();

        $result = $this->service->findById($employeePerformance->id);

        $this->assertInstanceOf(EmployeePerformance::class, $result);
        $this->assertEquals($employeePerformance->id, $result->id);
    }
}
