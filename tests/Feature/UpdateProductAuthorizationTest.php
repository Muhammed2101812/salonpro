<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateProductAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_user_without_permission_cannot_update_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/v1/products/{$product->id}", [
                'name' => 'New Name',
            ]);

        $response->assertForbidden();
    }

    public function test_user_with_permission_can_update_product(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('products.update');

        $product = Product::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/v1/products/{$product->id}", [
                'name' => 'New Name',
            ]);

        $response->assertOk();
    }

    public function test_super_admin_can_update_product(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $product = Product::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/v1/products/{$product->id}", [
                'name' => 'New Name',
            ]);

        $response->assertOk();
    }
}
