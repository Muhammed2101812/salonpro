<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ServicePriceHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePriceHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_servicePriceHistorys(): void
    {
        ServicePriceHistory::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/service-price-histories');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_servicePriceHistory(): void
    {
        $data = ServicePriceHistory::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/service-price-histories', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('service-price-histories', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_servicePriceHistory(): void
    {
        $servicePriceHistory = ServicePriceHistory::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/service-price-histories/{$servicePriceHistory->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_servicePriceHistory(): void
    {
        $servicePriceHistory = ServicePriceHistory::factory()->create();
        $updateData = ServicePriceHistory::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/service-price-histories/{$servicePriceHistory->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_servicePriceHistory(): void
    {
        $servicePriceHistory = ServicePriceHistory::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/service-price-histories/{$servicePriceHistory->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('service-price-histories', [
            'id' => $servicePriceHistory->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/service-price-histories');

        $response->assertUnauthorized();
    }
}
