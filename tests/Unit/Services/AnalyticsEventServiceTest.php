<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\AnalyticsEvent;
use App\Repositories\Contracts\AnalyticsEventRepositoryInterface;
use App\Services\AnalyticsEventService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsEventServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AnalyticsEventService $service;
    protected AnalyticsEventRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AnalyticsEventRepositoryInterface::class);
        $this->service = new AnalyticsEventService($this->repository);
    }

    public function test_can_get_all_analyticsEvents(): void
    {
        AnalyticsEvent::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_analyticsEvents(): void
    {
        AnalyticsEvent::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_analyticsEvent(): void
    {
        $data = AnalyticsEvent::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(AnalyticsEvent::class, $result);
        $this->assertDatabaseHas('analyticsEvents', ['id' => $result->id]);
    }

    public function test_can_update_analyticsEvent(): void
    {
        $analyticsEvent = AnalyticsEvent::factory()->create();
        $updateData = AnalyticsEvent::factory()->make()->toArray();

        $result = $this->service->update($analyticsEvent->id, $updateData);

        $this->assertInstanceOf(AnalyticsEvent::class, $result);
    }

    public function test_can_delete_analyticsEvent(): void
    {
        $analyticsEvent = AnalyticsEvent::factory()->create();

        $result = $this->service->delete($analyticsEvent->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('analyticsEvents', ['id' => $analyticsEvent->id]);
    }

    public function test_can_find_analyticsEvent_by_id(): void
    {
        $analyticsEvent = AnalyticsEvent::factory()->create();

        $result = $this->service->findById($analyticsEvent->id);

        $this->assertInstanceOf(AnalyticsEvent::class, $result);
        $this->assertEquals($analyticsEvent->id, $result->id);
    }
}
