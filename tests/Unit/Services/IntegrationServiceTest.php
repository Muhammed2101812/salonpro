<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Integration;
use App\Repositories\Contracts\IntegrationRepositoryInterface;
use App\Services\IntegrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected IntegrationService $service;
    protected IntegrationRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(IntegrationRepositoryInterface::class);
        $this->service = new IntegrationService($this->repository);
    }

    public function test_can_get_all_integrations(): void
    {
        Integration::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_integrations(): void
    {
        Integration::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_integration(): void
    {
        $data = Integration::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Integration::class, $result);
        $this->assertDatabaseHas('integrations', ['id' => $result->id]);
    }

    public function test_can_update_integration(): void
    {
        $integration = Integration::factory()->create();
        $updateData = Integration::factory()->make()->toArray();

        $result = $this->service->update($integration->id, $updateData);

        $this->assertInstanceOf(Integration::class, $result);
    }

    public function test_can_delete_integration(): void
    {
        $integration = Integration::factory()->create();

        $result = $this->service->delete($integration->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('integrations', ['id' => $integration->id]);
    }

    public function test_can_find_integration_by_id(): void
    {
        $integration = Integration::factory()->create();

        $result = $this->service->findById($integration->id);

        $this->assertInstanceOf(Integration::class, $result);
        $this->assertEquals($integration->id, $result->id);
    }
}
