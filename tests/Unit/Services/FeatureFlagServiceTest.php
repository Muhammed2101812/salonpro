<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\FeatureFlag;
use App\Repositories\Contracts\FeatureFlagRepositoryInterface;
use App\Services\FeatureFlagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureFlagServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FeatureFlagService $service;
    protected FeatureFlagRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(FeatureFlagRepositoryInterface::class);
        $this->service = new FeatureFlagService($this->repository);
    }

    public function test_can_get_all_featureFlags(): void
    {
        FeatureFlag::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_featureFlags(): void
    {
        FeatureFlag::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_featureFlag(): void
    {
        $data = FeatureFlag::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(FeatureFlag::class, $result);
        $this->assertDatabaseHas('featureFlags', ['id' => $result->id]);
    }

    public function test_can_update_featureFlag(): void
    {
        $featureFlag = FeatureFlag::factory()->create();
        $updateData = FeatureFlag::factory()->make()->toArray();

        $result = $this->service->update($featureFlag->id, $updateData);

        $this->assertInstanceOf(FeatureFlag::class, $result);
    }

    public function test_can_delete_featureFlag(): void
    {
        $featureFlag = FeatureFlag::factory()->create();

        $result = $this->service->delete($featureFlag->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('featureFlags', ['id' => $featureFlag->id]);
    }

    public function test_can_find_featureFlag_by_id(): void
    {
        $featureFlag = FeatureFlag::factory()->create();

        $result = $this->service->findById($featureFlag->id);

        $this->assertInstanceOf(FeatureFlag::class, $result);
        $this->assertEquals($featureFlag->id, $result->id);
    }
}
