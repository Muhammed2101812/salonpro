<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\PerformanceMetric;
use App\Repositories\Contracts\PerformanceMetricRepositoryInterface;
use App\Services\PerformanceMetricService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerformanceMetricServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PerformanceMetricService $service;
    protected PerformanceMetricRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(PerformanceMetricRepositoryInterface::class);
        $this->service = new PerformanceMetricService($this->repository);
    }

    public function test_can_get_all_performanceMetrics(): void
    {
        PerformanceMetric::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_performanceMetrics(): void
    {
        PerformanceMetric::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_performanceMetric(): void
    {
        $data = PerformanceMetric::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(PerformanceMetric::class, $result);
        $this->assertDatabaseHas('performanceMetrics', ['id' => $result->id]);
    }

    public function test_can_update_performanceMetric(): void
    {
        $performanceMetric = PerformanceMetric::factory()->create();
        $updateData = PerformanceMetric::factory()->make()->toArray();

        $result = $this->service->update($performanceMetric->id, $updateData);

        $this->assertInstanceOf(PerformanceMetric::class, $result);
    }

    public function test_can_delete_performanceMetric(): void
    {
        $performanceMetric = PerformanceMetric::factory()->create();

        $result = $this->service->delete($performanceMetric->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('performanceMetrics', ['id' => $performanceMetric->id]);
    }

    public function test_can_find_performanceMetric_by_id(): void
    {
        $performanceMetric = PerformanceMetric::factory()->create();

        $result = $this->service->findById($performanceMetric->id);

        $this->assertInstanceOf(PerformanceMetric::class, $result);
        $this->assertEquals($performanceMetric->id, $result->id);
    }
}
