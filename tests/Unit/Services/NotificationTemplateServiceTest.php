<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\NotificationTemplate;
use App\Repositories\Contracts\NotificationTemplateRepositoryInterface;
use App\Services\NotificationTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTemplateServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationTemplateService $service;
    protected NotificationTemplateRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(NotificationTemplateRepositoryInterface::class);
        $this->service = new NotificationTemplateService($this->repository);
    }

    public function test_can_get_all_notificationTemplates(): void
    {
        NotificationTemplate::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_notificationTemplates(): void
    {
        NotificationTemplate::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_notificationTemplate(): void
    {
        $data = NotificationTemplate::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(NotificationTemplate::class, $result);
        $this->assertDatabaseHas('notificationTemplates', ['id' => $result->id]);
    }

    public function test_can_update_notificationTemplate(): void
    {
        $notificationTemplate = NotificationTemplate::factory()->create();
        $updateData = NotificationTemplate::factory()->make()->toArray();

        $result = $this->service->update($notificationTemplate->id, $updateData);

        $this->assertInstanceOf(NotificationTemplate::class, $result);
    }

    public function test_can_delete_notificationTemplate(): void
    {
        $notificationTemplate = NotificationTemplate::factory()->create();

        $result = $this->service->delete($notificationTemplate->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('notificationTemplates', ['id' => $notificationTemplate->id]);
    }

    public function test_can_find_notificationTemplate_by_id(): void
    {
        $notificationTemplate = NotificationTemplate::factory()->create();

        $result = $this->service->findById($notificationTemplate->id);

        $this->assertInstanceOf(NotificationTemplate::class, $result);
        $this->assertEquals($notificationTemplate->id, $result->id);
    }
}
