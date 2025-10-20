<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\NotificationCampaign;
use App\Repositories\Eloquent\NotificationCampaignRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationCampaignRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationCampaignRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new NotificationCampaignRepository(new NotificationCampaign());
    }

    public function test_can_get_all_records(): void
    {
        NotificationCampaign::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = NotificationCampaign::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(NotificationCampaign::class, $result);
        $this->assertDatabaseHas('notificationCampaigns', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $notificationCampaign = NotificationCampaign::factory()->create();

        $result = $this->repository->find($notificationCampaign->id);

        $this->assertInstanceOf(NotificationCampaign::class, $result);
        $this->assertEquals($notificationCampaign->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $notificationCampaign = NotificationCampaign::factory()->create();
        $updateData = NotificationCampaign::factory()->make()->toArray();

        $result = $this->repository->update($notificationCampaign->id, $updateData);

        $this->assertInstanceOf(NotificationCampaign::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $notificationCampaign = NotificationCampaign::factory()->create();

        $result = $this->repository->delete($notificationCampaign->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('notificationCampaigns', ['id' => $notificationCampaign->id]);
    }
}
