<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\NotificationQueue;
use App\Repositories\Contracts\NotificationQueueRepositoryInterface;
use App\Services\NotificationQueueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationQueueServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationQueueService $service;
    protected NotificationQueueRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(NotificationQueueRepositoryInterface::class);
        $this->service = new NotificationQueueService($this->repository);
    }

    public function test_can_get_all_notificationQueues(): void
    {
        NotificationQueue::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_notificationQueues(): void
    {
        NotificationQueue::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_notificationQueue(): void
    {
        $data = NotificationQueue::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(NotificationQueue::class, $result);
        $this->assertDatabaseHas('notificationQueues', ['id' => $result->id]);
    }

    public function test_can_update_notificationQueue(): void
    {
        $notificationQueue = NotificationQueue::factory()->create();
        $updateData = NotificationQueue::factory()->make()->toArray();

        $result = $this->service->update($notificationQueue->id, $updateData);

        $this->assertInstanceOf(NotificationQueue::class, $result);
    }

    public function test_can_delete_notificationQueue(): void
    {
        $notificationQueue = NotificationQueue::factory()->create();

        $result = $this->service->delete($notificationQueue->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('notificationQueues', ['id' => $notificationQueue->id]);
    }

    public function test_can_find_notificationQueue_by_id(): void
    {
        $notificationQueue = NotificationQueue::factory()->create();

        $result = $this->service->findById($notificationQueue->id);

        $this->assertInstanceOf(NotificationQueue::class, $result);
        $this->assertEquals($notificationQueue->id, $result->id);
    }
}
