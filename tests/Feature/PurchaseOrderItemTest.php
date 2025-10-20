<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\PurchaseOrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderItemTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_purchaseOrderItems(): void
    {
        PurchaseOrderItem::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/purchase-order-items');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_purchaseOrderItem(): void
    {
        $data = PurchaseOrderItem::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/purchase-order-items', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('purchase-order-items', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_purchaseOrderItem(): void
    {
        $purchaseOrderItem = PurchaseOrderItem::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/purchase-order-items/{$purchaseOrderItem->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_purchaseOrderItem(): void
    {
        $purchaseOrderItem = PurchaseOrderItem::factory()->create();
        $updateData = PurchaseOrderItem::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/purchase-order-items/{$purchaseOrderItem->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_purchaseOrderItem(): void
    {
        $purchaseOrderItem = PurchaseOrderItem::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/purchase-order-items/{$purchaseOrderItem->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('purchase-order-items', [
            'id' => $purchaseOrderItem->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/purchase-order-items');

        $response->assertUnauthorized();
    }
}
