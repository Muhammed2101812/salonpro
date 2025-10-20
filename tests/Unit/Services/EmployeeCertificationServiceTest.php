<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\EmployeeCertification;
use App\Repositories\Contracts\EmployeeCertificationRepositoryInterface;
use App\Services\EmployeeCertificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeCertificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeCertificationService $service;
    protected EmployeeCertificationRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(EmployeeCertificationRepositoryInterface::class);
        $this->service = new EmployeeCertificationService($this->repository);
    }

    public function test_can_get_all_employeeCertifications(): void
    {
        EmployeeCertification::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_employeeCertifications(): void
    {
        EmployeeCertification::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_employeeCertification(): void
    {
        $data = EmployeeCertification::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(EmployeeCertification::class, $result);
        $this->assertDatabaseHas('employeeCertifications', ['id' => $result->id]);
    }

    public function test_can_update_employeeCertification(): void
    {
        $employeeCertification = EmployeeCertification::factory()->create();
        $updateData = EmployeeCertification::factory()->make()->toArray();

        $result = $this->service->update($employeeCertification->id, $updateData);

        $this->assertInstanceOf(EmployeeCertification::class, $result);
    }

    public function test_can_delete_employeeCertification(): void
    {
        $employeeCertification = EmployeeCertification::factory()->create();

        $result = $this->service->delete($employeeCertification->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('employeeCertifications', ['id' => $employeeCertification->id]);
    }

    public function test_can_find_employeeCertification_by_id(): void
    {
        $employeeCertification = EmployeeCertification::factory()->create();

        $result = $this->service->findById($employeeCertification->id);

        $this->assertInstanceOf(EmployeeCertification::class, $result);
        $this->assertEquals($employeeCertification->id, $result->id);
    }
}
