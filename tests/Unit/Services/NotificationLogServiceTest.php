<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\NotificationLog;
use App\Repositories\Contracts\NotificationLogRepositoryInterface;
use App\Services\NotificationLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationLogServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationLogService $service;
    protected NotificationLogRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(NotificationLogRepositoryInterface::class);
        $this->service = new NotificationLogService($this->repository);
    }

    public function test_can_get_all_notificationLogs(): void
    {
        NotificationLog::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_notificationLogs(): void
    {
        NotificationLog::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_notificationLog(): void
    {
        $data = NotificationLog::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(NotificationLog::class, $result);
        $this->assertDatabaseHas('notificationLogs', ['id' => $result->id]);
    }

    public function test_can_update_notificationLog(): void
    {
        $notificationLog = NotificationLog::factory()->create();
        $updateData = NotificationLog::factory()->make()->toArray();

        $result = $this->service->update($notificationLog->id, $updateData);

        $this->assertInstanceOf(NotificationLog::class, $result);
    }

    public function test_can_delete_notificationLog(): void
    {
        $notificationLog = NotificationLog::factory()->create();

        $result = $this->service->delete($notificationLog->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('notificationLogs', ['id' => $notificationLog->id]);
    }

    public function test_can_find_notificationLog_by_id(): void
    {
        $notificationLog = NotificationLog::factory()->create();

        $result = $this->service->findById($notificationLog->id);

        $this->assertInstanceOf(NotificationLog::class, $result);
        $this->assertEquals($notificationLog->id, $result->id);
    }
}
