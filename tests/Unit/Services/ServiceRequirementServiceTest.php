<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Requirement;
use App\Repositories\Contracts\RequirementRepositoryInterface;
use App\Services\ServiceRequirementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRequirementServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceRequirementService $service;
    protected RequirementRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(RequirementRepositoryInterface::class);
        $this->service = new ServiceRequirementService($this->repository);
    }

    public function test_can_get_all_requirements(): void
    {
        Requirement::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_requirements(): void
    {
        Requirement::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_requirement(): void
    {
        $data = Requirement::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Requirement::class, $result);
        $this->assertDatabaseHas('requirements', ['id' => $result->id]);
    }

    public function test_can_update_requirement(): void
    {
        $requirement = Requirement::factory()->create();
        $updateData = Requirement::factory()->make()->toArray();

        $result = $this->service->update($requirement->id, $updateData);

        $this->assertInstanceOf(Requirement::class, $result);
    }

    public function test_can_delete_requirement(): void
    {
        $requirement = Requirement::factory()->create();

        $result = $this->service->delete($requirement->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('requirements', ['id' => $requirement->id]);
    }

    public function test_can_find_requirement_by_id(): void
    {
        $requirement = Requirement::factory()->create();

        $result = $this->service->findById($requirement->id);

        $this->assertInstanceOf(Requirement::class, $result);
        $this->assertEquals($requirement->id, $result->id);
    }
}
