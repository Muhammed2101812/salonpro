<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\NotificationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_notificationTemplates(): void
    {
        NotificationTemplate::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/notification-templates');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_notificationTemplate(): void
    {
        $data = NotificationTemplate::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/notification-templates', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('notification-templates', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_notificationTemplate(): void
    {
        $notificationTemplate = NotificationTemplate::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/notification-templates/{$notificationTemplate->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_notificationTemplate(): void
    {
        $notificationTemplate = NotificationTemplate::factory()->create();
        $updateData = NotificationTemplate::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/notification-templates/{$notificationTemplate->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_notificationTemplate(): void
    {
        $notificationTemplate = NotificationTemplate::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/notification-templates/{$notificationTemplate->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('notification-templates', [
            'id' => $notificationTemplate->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/notification-templates');

        $response->assertUnauthorized();
    }
}
