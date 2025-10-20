<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ProductSupplierPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSupplierPriceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_productSupplierPrices(): void
    {
        ProductSupplierPrice::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/product-supplier-prices');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_productSupplierPrice(): void
    {
        $data = ProductSupplierPrice::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/product-supplier-prices', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('product-supplier-prices', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_productSupplierPrice(): void
    {
        $productSupplierPrice = ProductSupplierPrice::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/product-supplier-prices/{$productSupplierPrice->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_productSupplierPrice(): void
    {
        $productSupplierPrice = ProductSupplierPrice::factory()->create();
        $updateData = ProductSupplierPrice::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/product-supplier-prices/{$productSupplierPrice->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_productSupplierPrice(): void
    {
        $productSupplierPrice = ProductSupplierPrice::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/product-supplier-prices/{$productSupplierPrice->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('product-supplier-prices', [
            'id' => $productSupplierPrice->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/product-supplier-prices');

        $response->assertUnauthorized();
    }
}
