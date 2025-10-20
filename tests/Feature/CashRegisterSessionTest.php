<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CashRegisterSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashRegisterSessionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_cashRegisterSessions(): void
    {
        CashRegisterSession::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/cash-register-sessions');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_cashRegisterSession(): void
    {
        $data = CashRegisterSession::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/cash-register-sessions', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('cash-register-sessions', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_cashRegisterSession(): void
    {
        $cashRegisterSession = CashRegisterSession::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/cash-register-sessions/{$cashRegisterSession->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_cashRegisterSession(): void
    {
        $cashRegisterSession = CashRegisterSession::factory()->create();
        $updateData = CashRegisterSession::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/cash-register-sessions/{$cashRegisterSession->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_cashRegisterSession(): void
    {
        $cashRegisterSession = CashRegisterSession::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/cash-register-sessions/{$cashRegisterSession->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('cash-register-sessions', [
            'id' => $cashRegisterSession->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/cash-register-sessions');

        $response->assertUnauthorized();
    }
}
