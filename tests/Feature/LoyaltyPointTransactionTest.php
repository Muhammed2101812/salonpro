<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\LoyaltyPointTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyPointTransactionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_loyaltyPointTransactions(): void
    {
        LoyaltyPointTransaction::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/loyalty-point-transactions');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_loyaltyPointTransaction(): void
    {
        $data = LoyaltyPointTransaction::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/loyalty-point-transactions', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('loyalty-point-transactions', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_loyaltyPointTransaction(): void
    {
        $loyaltyPointTransaction = LoyaltyPointTransaction::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/loyalty-point-transactions/{$loyaltyPointTransaction->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_loyaltyPointTransaction(): void
    {
        $loyaltyPointTransaction = LoyaltyPointTransaction::factory()->create();
        $updateData = LoyaltyPointTransaction::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/loyalty-point-transactions/{$loyaltyPointTransaction->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_loyaltyPointTransaction(): void
    {
        $loyaltyPointTransaction = LoyaltyPointTransaction::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/loyalty-point-transactions/{$loyaltyPointTransaction->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('loyalty-point-transactions', [
            'id' => $loyaltyPointTransaction->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/loyalty-point-transactions');

        $response->assertUnauthorized();
    }
}
