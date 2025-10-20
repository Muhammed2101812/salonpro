<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\EmployeeCommission;
use App\Repositories\Eloquent\EmployeeCommissionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeCommissionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeCommissionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EmployeeCommissionRepository(new EmployeeCommission());
    }

    public function test_can_get_all_records(): void
    {
        EmployeeCommission::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = EmployeeCommission::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(EmployeeCommission::class, $result);
        $this->assertDatabaseHas('employeeCommissions', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $employeeCommission = EmployeeCommission::factory()->create();

        $result = $this->repository->find($employeeCommission->id);

        $this->assertInstanceOf(EmployeeCommission::class, $result);
        $this->assertEquals($employeeCommission->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $employeeCommission = EmployeeCommission::factory()->create();
        $updateData = EmployeeCommission::factory()->make()->toArray();

        $result = $this->repository->update($employeeCommission->id, $updateData);

        $this->assertInstanceOf(EmployeeCommission::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $employeeCommission = EmployeeCommission::factory()->create();

        $result = $this->repository->delete($employeeCommission->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('employeeCommissions', ['id' => $employeeCommission->id]);
    }
}
