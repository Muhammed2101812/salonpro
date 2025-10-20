<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ProductAttributeValue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAttributeValueTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_productAttributeValues(): void
    {
        ProductAttributeValue::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/product-attribute-values');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_productAttributeValue(): void
    {
        $data = ProductAttributeValue::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/product-attribute-values', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('product-attribute-values', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_productAttributeValue(): void
    {
        $productAttributeValue = ProductAttributeValue::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/product-attribute-values/{$productAttributeValue->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_productAttributeValue(): void
    {
        $productAttributeValue = ProductAttributeValue::factory()->create();
        $updateData = ProductAttributeValue::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/product-attribute-values/{$productAttributeValue->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_productAttributeValue(): void
    {
        $productAttributeValue = ProductAttributeValue::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/product-attribute-values/{$productAttributeValue->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('product-attribute-values', [
            'id' => $productAttributeValue->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/product-attribute-values');

        $response->assertUnauthorized();
    }
}
