<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\NotificationCampaign;
use App\Repositories\Contracts\NotificationCampaignRepositoryInterface;
use App\Services\NotificationCampaignService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationCampaignServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationCampaignService $service;
    protected NotificationCampaignRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(NotificationCampaignRepositoryInterface::class);
        $this->service = new NotificationCampaignService($this->repository);
    }

    public function test_can_get_all_notificationCampaigns(): void
    {
        NotificationCampaign::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_notificationCampaigns(): void
    {
        NotificationCampaign::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_notificationCampaign(): void
    {
        $data = NotificationCampaign::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(NotificationCampaign::class, $result);
        $this->assertDatabaseHas('notificationCampaigns', ['id' => $result->id]);
    }

    public function test_can_update_notificationCampaign(): void
    {
        $notificationCampaign = NotificationCampaign::factory()->create();
        $updateData = NotificationCampaign::factory()->make()->toArray();

        $result = $this->service->update($notificationCampaign->id, $updateData);

        $this->assertInstanceOf(NotificationCampaign::class, $result);
    }

    public function test_can_delete_notificationCampaign(): void
    {
        $notificationCampaign = NotificationCampaign::factory()->create();

        $result = $this->service->delete($notificationCampaign->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('notificationCampaigns', ['id' => $notificationCampaign->id]);
    }

    public function test_can_find_notificationCampaign_by_id(): void
    {
        $notificationCampaign = NotificationCampaign::factory()->create();

        $result = $this->service->findById($notificationCampaign->id);

        $this->assertInstanceOf(NotificationCampaign::class, $result);
        $this->assertEquals($notificationCampaign->id, $result->id);
    }
}
