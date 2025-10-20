<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\NotificationQueue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationQueueTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_notificationQueues(): void
    {
        NotificationQueue::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/notification-queues');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_notificationQueue(): void
    {
        $data = NotificationQueue::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/notification-queues', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('notification-queues', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_notificationQueue(): void
    {
        $notificationQueue = NotificationQueue::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/notification-queues/{$notificationQueue->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_notificationQueue(): void
    {
        $notificationQueue = NotificationQueue::factory()->create();
        $updateData = NotificationQueue::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/notification-queues/{$notificationQueue->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_notificationQueue(): void
    {
        $notificationQueue = NotificationQueue::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/notification-queues/{$notificationQueue->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('notification-queues', [
            'id' => $notificationQueue->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/notification-queues');

        $response->assertUnauthorized();
    }
}
