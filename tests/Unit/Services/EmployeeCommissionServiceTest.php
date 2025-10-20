<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\EmployeeCommission;
use App\Repositories\Contracts\EmployeeCommissionRepositoryInterface;
use App\Services\EmployeeCommissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeCommissionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeCommissionService $service;
    protected EmployeeCommissionRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(EmployeeCommissionRepositoryInterface::class);
        $this->service = new EmployeeCommissionService($this->repository);
    }

    public function test_can_get_all_employeeCommissions(): void
    {
        EmployeeCommission::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_employeeCommissions(): void
    {
        EmployeeCommission::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_employeeCommission(): void
    {
        $data = EmployeeCommission::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(EmployeeCommission::class, $result);
        $this->assertDatabaseHas('employeeCommissions', ['id' => $result->id]);
    }

    public function test_can_update_employeeCommission(): void
    {
        $employeeCommission = EmployeeCommission::factory()->create();
        $updateData = EmployeeCommission::factory()->make()->toArray();

        $result = $this->service->update($employeeCommission->id, $updateData);

        $this->assertInstanceOf(EmployeeCommission::class, $result);
    }

    public function test_can_delete_employeeCommission(): void
    {
        $employeeCommission = EmployeeCommission::factory()->create();

        $result = $this->service->delete($employeeCommission->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('employeeCommissions', ['id' => $employeeCommission->id]);
    }

    public function test_can_find_employeeCommission_by_id(): void
    {
        $employeeCommission = EmployeeCommission::factory()->create();

        $result = $this->service->findById($employeeCommission->id);

        $this->assertInstanceOf(EmployeeCommission::class, $result);
        $this->assertEquals($employeeCommission->id, $result->id);
    }
}
