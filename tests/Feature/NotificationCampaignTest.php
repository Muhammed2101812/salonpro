<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\NotificationCampaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationCampaignTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_notificationCampaigns(): void
    {
        NotificationCampaign::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/notification-campaigns');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_notificationCampaign(): void
    {
        $data = NotificationCampaign::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/notification-campaigns', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('notification-campaigns', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_notificationCampaign(): void
    {
        $notificationCampaign = NotificationCampaign::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/notification-campaigns/{$notificationCampaign->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_notificationCampaign(): void
    {
        $notificationCampaign = NotificationCampaign::factory()->create();
        $updateData = NotificationCampaign::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/notification-campaigns/{$notificationCampaign->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_notificationCampaign(): void
    {
        $notificationCampaign = NotificationCampaign::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/notification-campaigns/{$notificationCampaign->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('notification-campaigns', [
            'id' => $notificationCampaign->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/notification-campaigns');

        $response->assertUnauthorized();
    }
}
