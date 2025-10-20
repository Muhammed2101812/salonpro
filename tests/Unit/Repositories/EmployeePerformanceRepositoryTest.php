<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\EmployeePerformance;
use App\Repositories\Eloquent\EmployeePerformanceRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeePerformanceRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeePerformanceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EmployeePerformanceRepository(new EmployeePerformance());
    }

    public function test_can_get_all_records(): void
    {
        EmployeePerformance::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = EmployeePerformance::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(EmployeePerformance::class, $result);
        $this->assertDatabaseHas('employeePerformances', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $employeePerformance = EmployeePerformance::factory()->create();

        $result = $this->repository->find($employeePerformance->id);

        $this->assertInstanceOf(EmployeePerformance::class, $result);
        $this->assertEquals($employeePerformance->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $employeePerformance = EmployeePerformance::factory()->create();
        $updateData = EmployeePerformance::factory()->make()->toArray();

        $result = $this->repository->update($employeePerformance->id, $updateData);

        $this->assertInstanceOf(EmployeePerformance::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $employeePerformance = EmployeePerformance::factory()->create();

        $result = $this->repository->delete($employeePerformance->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('employeePerformances', ['id' => $employeePerformance->id]);
    }
}
