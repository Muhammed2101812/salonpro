<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\NotificationLog;
use App\Repositories\Eloquent\NotificationLogRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationLogRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationLogRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new NotificationLogRepository(new NotificationLog());
    }

    public function test_can_get_all_records(): void
    {
        NotificationLog::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = NotificationLog::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(NotificationLog::class, $result);
        $this->assertDatabaseHas('notificationLogs', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $notificationLog = NotificationLog::factory()->create();

        $result = $this->repository->find($notificationLog->id);

        $this->assertInstanceOf(NotificationLog::class, $result);
        $this->assertEquals($notificationLog->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $notificationLog = NotificationLog::factory()->create();
        $updateData = NotificationLog::factory()->make()->toArray();

        $result = $this->repository->update($notificationLog->id, $updateData);

        $this->assertInstanceOf(NotificationLog::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $notificationLog = NotificationLog::factory()->create();

        $result = $this->repository->delete($notificationLog->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('notificationLogs', ['id' => $notificationLog->id]);
    }
}
