<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\StockAuditItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAuditItemTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_stockAuditItems(): void
    {
        StockAuditItem::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/stock-audit-items');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_stockAuditItem(): void
    {
        $data = StockAuditItem::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/stock-audit-items', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('stock-audit-items', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_stockAuditItem(): void
    {
        $stockAuditItem = StockAuditItem::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/stock-audit-items/{$stockAuditItem->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_stockAuditItem(): void
    {
        $stockAuditItem = StockAuditItem::factory()->create();
        $updateData = StockAuditItem::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/stock-audit-items/{$stockAuditItem->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_stockAuditItem(): void
    {
        $stockAuditItem = StockAuditItem::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/stock-audit-items/{$stockAuditItem->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('stock-audit-items', [
            'id' => $stockAuditItem->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/stock-audit-items');

        $response->assertUnauthorized();
    }
}
