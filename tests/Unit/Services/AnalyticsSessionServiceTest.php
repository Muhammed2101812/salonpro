<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\AnalyticsSession;
use App\Repositories\Contracts\AnalyticsSessionRepositoryInterface;
use App\Services\AnalyticsSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsSessionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AnalyticsSessionService $service;
    protected AnalyticsSessionRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AnalyticsSessionRepositoryInterface::class);
        $this->service = new AnalyticsSessionService($this->repository);
    }

    public function test_can_get_all_analyticsSessions(): void
    {
        AnalyticsSession::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_analyticsSessions(): void
    {
        AnalyticsSession::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_analyticsSession(): void
    {
        $data = AnalyticsSession::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(AnalyticsSession::class, $result);
        $this->assertDatabaseHas('analyticsSessions', ['id' => $result->id]);
    }

    public function test_can_update_analyticsSession(): void
    {
        $analyticsSession = AnalyticsSession::factory()->create();
        $updateData = AnalyticsSession::factory()->make()->toArray();

        $result = $this->service->update($analyticsSession->id, $updateData);

        $this->assertInstanceOf(AnalyticsSession::class, $result);
    }

    public function test_can_delete_analyticsSession(): void
    {
        $analyticsSession = AnalyticsSession::factory()->create();

        $result = $this->service->delete($analyticsSession->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('analyticsSessions', ['id' => $analyticsSession->id]);
    }

    public function test_can_find_analyticsSession_by_id(): void
    {
        $analyticsSession = AnalyticsSession::factory()->create();

        $result = $this->service->findById($analyticsSession->id);

        $this->assertInstanceOf(AnalyticsSession::class, $result);
        $this->assertEquals($analyticsSession->id, $result->id);
    }
}
