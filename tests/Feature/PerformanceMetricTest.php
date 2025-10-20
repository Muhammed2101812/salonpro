<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\PerformanceMetric;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerformanceMetricTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_performanceMetrics(): void
    {
        PerformanceMetric::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/performance-metrics');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_performanceMetric(): void
    {
        $data = PerformanceMetric::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/performance-metrics', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('performance-metrics', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_performanceMetric(): void
    {
        $performanceMetric = PerformanceMetric::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/performance-metrics/{$performanceMetric->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_performanceMetric(): void
    {
        $performanceMetric = PerformanceMetric::factory()->create();
        $updateData = PerformanceMetric::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/performance-metrics/{$performanceMetric->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_performanceMetric(): void
    {
        $performanceMetric = PerformanceMetric::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/performance-metrics/{$performanceMetric->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('performance-metrics', [
            'id' => $performanceMetric->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/performance-metrics');

        $response->assertUnauthorized();
    }
}
