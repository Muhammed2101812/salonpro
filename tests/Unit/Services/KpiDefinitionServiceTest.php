<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\KpiDefinition;
use App\Repositories\Contracts\KpiDefinitionRepositoryInterface;
use App\Services\KpiDefinitionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KpiDefinitionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected KpiDefinitionService $service;
    protected KpiDefinitionRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(KpiDefinitionRepositoryInterface::class);
        $this->service = new KpiDefinitionService($this->repository);
    }

    public function test_can_get_all_kpiDefinitions(): void
    {
        KpiDefinition::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_kpiDefinitions(): void
    {
        KpiDefinition::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_kpiDefinition(): void
    {
        $data = KpiDefinition::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(KpiDefinition::class, $result);
        $this->assertDatabaseHas('kpiDefinitions', ['id' => $result->id]);
    }

    public function test_can_update_kpiDefinition(): void
    {
        $kpiDefinition = KpiDefinition::factory()->create();
        $updateData = KpiDefinition::factory()->make()->toArray();

        $result = $this->service->update($kpiDefinition->id, $updateData);

        $this->assertInstanceOf(KpiDefinition::class, $result);
    }

    public function test_can_delete_kpiDefinition(): void
    {
        $kpiDefinition = KpiDefinition::factory()->create();

        $result = $this->service->delete($kpiDefinition->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('kpiDefinitions', ['id' => $kpiDefinition->id]);
    }

    public function test_can_find_kpiDefinition_by_id(): void
    {
        $kpiDefinition = KpiDefinition::factory()->create();

        $result = $this->service->findById($kpiDefinition->id);

        $this->assertInstanceOf(KpiDefinition::class, $result);
        $this->assertEquals($kpiDefinition->id, $result->id);
    }
}
