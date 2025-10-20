<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\NotificationPreference;
use App\Repositories\Eloquent\NotificationPreferenceRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationPreferenceRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationPreferenceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new NotificationPreferenceRepository(new NotificationPreference());
    }

    public function test_can_get_all_records(): void
    {
        NotificationPreference::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = NotificationPreference::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(NotificationPreference::class, $result);
        $this->assertDatabaseHas('notificationPreferences', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $notificationPreference = NotificationPreference::factory()->create();

        $result = $this->repository->find($notificationPreference->id);

        $this->assertInstanceOf(NotificationPreference::class, $result);
        $this->assertEquals($notificationPreference->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $notificationPreference = NotificationPreference::factory()->create();
        $updateData = NotificationPreference::factory()->make()->toArray();

        $result = $this->repository->update($notificationPreference->id, $updateData);

        $this->assertInstanceOf(NotificationPreference::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $notificationPreference = NotificationPreference::factory()->create();

        $result = $this->repository->delete($notificationPreference->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('notificationPreferences', ['id' => $notificationPreference->id]);
    }
}
