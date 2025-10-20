<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ProductBundleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductBundleItemTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_productBundleItems(): void
    {
        ProductBundleItem::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/product-bundle-items');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_productBundleItem(): void
    {
        $data = ProductBundleItem::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/product-bundle-items', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('product-bundle-items', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_productBundleItem(): void
    {
        $productBundleItem = ProductBundleItem::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/product-bundle-items/{$productBundleItem->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_productBundleItem(): void
    {
        $productBundleItem = ProductBundleItem::factory()->create();
        $updateData = ProductBundleItem::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/product-bundle-items/{$productBundleItem->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_productBundleItem(): void
    {
        $productBundleItem = ProductBundleItem::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/product-bundle-items/{$productBundleItem->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('product-bundle-items', [
            'id' => $productBundleItem->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/product-bundle-items');

        $response->assertUnauthorized();
    }
}
