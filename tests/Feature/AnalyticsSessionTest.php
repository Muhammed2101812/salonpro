<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AnalyticsSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsSessionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_analyticsSessions(): void
    {
        AnalyticsSession::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/analytics-sessions');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_analyticsSession(): void
    {
        $data = AnalyticsSession::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/analytics-sessions', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('analytics-sessions', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_analyticsSession(): void
    {
        $analyticsSession = AnalyticsSession::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/analytics-sessions/{$analyticsSession->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_analyticsSession(): void
    {
        $analyticsSession = AnalyticsSession::factory()->create();
        $updateData = AnalyticsSession::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/analytics-sessions/{$analyticsSession->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_analyticsSession(): void
    {
        $analyticsSession = AnalyticsSession::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/analytics-sessions/{$analyticsSession->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('analytics-sessions', [
            'id' => $analyticsSession->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/analytics-sessions');

        $response->assertUnauthorized();
    }
}
