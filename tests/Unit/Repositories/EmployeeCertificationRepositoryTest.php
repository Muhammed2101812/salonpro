<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\EmployeeCertification;
use App\Repositories\Eloquent\EmployeeCertificationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeCertificationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeCertificationRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EmployeeCertificationRepository(new EmployeeCertification());
    }

    public function test_can_get_all_records(): void
    {
        EmployeeCertification::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = EmployeeCertification::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(EmployeeCertification::class, $result);
        $this->assertDatabaseHas('employeeCertifications', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $employeeCertification = EmployeeCertification::factory()->create();

        $result = $this->repository->find($employeeCertification->id);

        $this->assertInstanceOf(EmployeeCertification::class, $result);
        $this->assertEquals($employeeCertification->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $employeeCertification = EmployeeCertification::factory()->create();
        $updateData = EmployeeCertification::factory()->make()->toArray();

        $result = $this->repository->update($employeeCertification->id, $updateData);

        $this->assertInstanceOf(EmployeeCertification::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $employeeCertification = EmployeeCertification::factory()->create();

        $result = $this->repository->delete($employeeCertification->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('employeeCertifications', ['id' => $employeeCertification->id]);
    }
}
