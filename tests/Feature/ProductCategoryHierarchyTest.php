<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ProductCategoryHierarchy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCategoryHierarchyTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_productCategoryHierarchys(): void
    {
        ProductCategoryHierarchy::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/product-category-hierarchies');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_productCategoryHierarchy(): void
    {
        $data = ProductCategoryHierarchy::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/product-category-hierarchies', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('product-category-hierarchies', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_productCategoryHierarchy(): void
    {
        $productCategoryHierarchy = ProductCategoryHierarchy::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/product-category-hierarchies/{$productCategoryHierarchy->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_productCategoryHierarchy(): void
    {
        $productCategoryHierarchy = ProductCategoryHierarchy::factory()->create();
        $updateData = ProductCategoryHierarchy::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/product-category-hierarchies/{$productCategoryHierarchy->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_productCategoryHierarchy(): void
    {
        $productCategoryHierarchy = ProductCategoryHierarchy::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/product-category-hierarchies/{$productCategoryHierarchy->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('product-category-hierarchies', [
            'id' => $productCategoryHierarchy->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/product-category-hierarchies');

        $response->assertUnauthorized();
    }
}
