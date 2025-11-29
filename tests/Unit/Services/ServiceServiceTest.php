<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Service;
use App\Repositories\Contracts\RepositoryInterface;
use App\Services\ServiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceService $service;
    protected RepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(RepositoryInterface::class);
        $this->service = new ServiceService($this->repository);
    }

    public function test_can_get_all_services(): void
    {
        Service::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_services(): void
    {
        Service::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_service(): void
    {
        $data = Service::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Service::class, $result);
        $this->assertDatabaseHas('services', ['id' => $result->id]);
    }

    public function test_can_update_service(): void
    {
        $service = Service::factory()->create();
        $updateData = Service::factory()->make()->toArray();

        $result = $this->service->update($service->id, $updateData);

        $this->assertInstanceOf(Service::class, $result);
    }

    public function test_can_delete_service(): void
    {
        $service = Service::factory()->create();

        $result = $this->service->delete($service->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }

    public function test_can_find_service_by_id(): void
    {
        $service = Service::factory()->create();

        $result = $this->service->findById($service->id);

        $this->assertInstanceOf(Service::class, $result);
        $this->assertEquals($service->id, $result->id);
    }
}
