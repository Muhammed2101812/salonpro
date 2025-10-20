<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\LoyaltyPoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyPointTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_loyaltyPoints(): void
    {
        LoyaltyPoint::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/loyalty-points');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_loyaltyPoint(): void
    {
        $data = LoyaltyPoint::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/loyalty-points', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('loyalty-points', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_loyaltyPoint(): void
    {
        $loyaltyPoint = LoyaltyPoint::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/loyalty-points/{$loyaltyPoint->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_loyaltyPoint(): void
    {
        $loyaltyPoint = LoyaltyPoint::factory()->create();
        $updateData = LoyaltyPoint::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/loyalty-points/{$loyaltyPoint->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_loyaltyPoint(): void
    {
        $loyaltyPoint = LoyaltyPoint::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/loyalty-points/{$loyaltyPoint->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('loyalty-points', [
            'id' => $loyaltyPoint->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/loyalty-points');

        $response->assertUnauthorized();
    }
}
