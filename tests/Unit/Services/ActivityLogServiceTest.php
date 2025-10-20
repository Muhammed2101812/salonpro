<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ActivityLog;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use App\Services\ActivityLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ActivityLogService $service;
    protected ActivityLogRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ActivityLogRepositoryInterface::class);
        $this->service = new ActivityLogService($this->repository);
    }

    public function test_can_get_all_activityLogs(): void
    {
        ActivityLog::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_activityLogs(): void
    {
        ActivityLog::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_activityLog(): void
    {
        $data = ActivityLog::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ActivityLog::class, $result);
        $this->assertDatabaseHas('activityLogs', ['id' => $result->id]);
    }

    public function test_can_update_activityLog(): void
    {
        $activityLog = ActivityLog::factory()->create();
        $updateData = ActivityLog::factory()->make()->toArray();

        $result = $this->service->update($activityLog->id, $updateData);

        $this->assertInstanceOf(ActivityLog::class, $result);
    }

    public function test_can_delete_activityLog(): void
    {
        $activityLog = ActivityLog::factory()->create();

        $result = $this->service->delete($activityLog->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('activityLogs', ['id' => $activityLog->id]);
    }

    public function test_can_find_activityLog_by_id(): void
    {
        $activityLog = ActivityLog::factory()->create();

        $result = $this->service->findById($activityLog->id);

        $this->assertInstanceOf(ActivityLog::class, $result);
        $this->assertEquals($activityLog->id, $result->id);
    }
}
