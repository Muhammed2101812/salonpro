<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ProductBarcode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductBarcodeTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_productBarcodes(): void
    {
        ProductBarcode::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/product-barcodes');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_productBarcode(): void
    {
        $data = ProductBarcode::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/product-barcodes', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('product-barcodes', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_productBarcode(): void
    {
        $productBarcode = ProductBarcode::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/product-barcodes/{$productBarcode->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_productBarcode(): void
    {
        $productBarcode = ProductBarcode::factory()->create();
        $updateData = ProductBarcode::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/product-barcodes/{$productBarcode->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_productBarcode(): void
    {
        $productBarcode = ProductBarcode::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/product-barcodes/{$productBarcode->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('product-barcodes', [
            'id' => $productBarcode->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/product-barcodes');

        $response->assertUnauthorized();
    }
}
