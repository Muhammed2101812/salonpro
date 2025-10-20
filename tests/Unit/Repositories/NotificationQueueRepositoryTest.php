<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\NotificationQueue;
use App\Repositories\Eloquent\NotificationQueueRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationQueueRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationQueueRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new NotificationQueueRepository(new NotificationQueue());
    }

    public function test_can_get_all_records(): void
    {
        NotificationQueue::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = NotificationQueue::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(NotificationQueue::class, $result);
        $this->assertDatabaseHas('notificationQueues', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $notificationQueue = NotificationQueue::factory()->create();

        $result = $this->repository->find($notificationQueue->id);

        $this->assertInstanceOf(NotificationQueue::class, $result);
        $this->assertEquals($notificationQueue->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $notificationQueue = NotificationQueue::factory()->create();
        $updateData = NotificationQueue::factory()->make()->toArray();

        $result = $this->repository->update($notificationQueue->id, $updateData);

        $this->assertInstanceOf(NotificationQueue::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $notificationQueue = NotificationQueue::factory()->create();

        $result = $this->repository->delete($notificationQueue->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('notificationQueues', ['id' => $notificationQueue->id]);
    }
}
