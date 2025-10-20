<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\NotificationPreference;
use App\Repositories\Contracts\NotificationPreferenceRepositoryInterface;
use App\Services\NotificationPreferenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationPreferenceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationPreferenceService $service;
    protected NotificationPreferenceRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(NotificationPreferenceRepositoryInterface::class);
        $this->service = new NotificationPreferenceService($this->repository);
    }

    public function test_can_get_all_notificationPreferences(): void
    {
        NotificationPreference::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_notificationPreferences(): void
    {
        NotificationPreference::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_notificationPreference(): void
    {
        $data = NotificationPreference::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(NotificationPreference::class, $result);
        $this->assertDatabaseHas('notificationPreferences', ['id' => $result->id]);
    }

    public function test_can_update_notificationPreference(): void
    {
        $notificationPreference = NotificationPreference::factory()->create();
        $updateData = NotificationPreference::factory()->make()->toArray();

        $result = $this->service->update($notificationPreference->id, $updateData);

        $this->assertInstanceOf(NotificationPreference::class, $result);
    }

    public function test_can_delete_notificationPreference(): void
    {
        $notificationPreference = NotificationPreference::factory()->create();

        $result = $this->service->delete($notificationPreference->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('notificationPreferences', ['id' => $notificationPreference->id]);
    }

    public function test_can_find_notificationPreference_by_id(): void
    {
        $notificationPreference = NotificationPreference::factory()->create();

        $result = $this->service->findById($notificationPreference->id);

        $this->assertInstanceOf(NotificationPreference::class, $result);
        $this->assertEquals($notificationPreference->id, $result->id);
    }
}
