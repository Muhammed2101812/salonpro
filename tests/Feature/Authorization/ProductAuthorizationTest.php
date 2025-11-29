<?php

declare(strict_types=1);

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;
    private Branch $branch2;
    private User $superAdmin;
    private User $inventoryManager;
    private User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create(['name' => 'Branch 1']);
        $this->branch2 = Branch::factory()->create(['name' => 'Branch 2']);

        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $inventoryManagerRole = Role::create(['name' => 'Inventory Manager']);
        $viewerRole = Role::create(['name' => 'Viewer']);

        Permission::create(['name' => 'products.view']);
        Permission::create(['name' => 'products.create']);
        Permission::create(['name' => 'products.update']);
        Permission::create(['name' => 'products.delete']);

        $superAdminRole->givePermissionTo(['products.view', 'products.create', 'products.update', 'products.delete']);
        $inventoryManagerRole->givePermissionTo(['products.view', 'products.create', 'products.update', 'products.delete']);
        $viewerRole->givePermissionTo(['products.view']);

        $this->superAdmin = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->superAdmin->assignRole($superAdminRole);

        $this->inventoryManager = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->inventoryManager->assignRole($inventoryManagerRole);

        $this->viewer = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->viewer->assignRole($viewerRole);
    }

    public function test_inventory_manager_can_create_product(): void
    {
        $productData = [
            'branch_id' => $this->branch->id,
            'name' => 'Shampoo',
            'sku' => 'SHP-001',
            'price' => 25.99,
            'cost_price' => 15.00,
            'stock_quantity' => 50,
            'unit' => 'bottle',
        ];

        $response = $this->actingAs($this->inventoryManager, 'sanctum')
            ->postJson('/api/v1/products', $productData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', [
            'name' => 'Shampoo',
            'sku' => 'SHP-001',
        ]);
    }

    public function test_viewer_cannot_create_product(): void
    {
        $productData = [
            'branch_id' => $this->branch->id,
            'name' => 'Conditioner',
            'sku' => 'CND-001',
            'price' => 20.99,
        ];

        $response = $this->actingAs($this->viewer, 'sanctum')
            ->postJson('/api/v1/products', $productData);

        $response->assertStatus(403);
    }

    public function test_inventory_manager_can_update_own_branch_product(): void
    {
        $product = Product::factory()->create(['branch_id' => $this->branch->id]);

        $updateData = [
            'name' => 'Updated Product',
            'price' => 35.99,
        ];

        $response = $this->actingAs($this->inventoryManager, 'sanctum')
            ->putJson("/api/v1/products/{$product->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
        ]);
    }

    public function test_inventory_manager_cannot_update_other_branch_product(): void
    {
        $product = Product::factory()->create(['branch_id' => $this->branch2->id]);

        $updateData = [
            'name' => 'Updated Product',
        ];

        $response = $this->actingAs($this->inventoryManager, 'sanctum')
            ->putJson("/api/v1/products/{$product->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_super_admin_can_update_any_branch_product(): void
    {
        $product = Product::factory()->create(['branch_id' => $this->branch2->id]);

        $updateData = [
            'name' => 'Super Admin Updated',
        ];

        $response = $this->actingAs($this->superAdmin, 'sanctum')
            ->putJson("/api/v1/products/{$product->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Super Admin Updated',
        ]);
    }

    public function test_inventory_manager_can_delete_own_branch_product(): void
    {
        $product = Product::factory()->create(['branch_id' => $this->branch->id]);

        $response = $this->actingAs($this->inventoryManager, 'sanctum')
            ->deleteJson("/api/v1/products/{$product->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_viewer_cannot_delete_product(): void
    {
        $product = Product::factory()->create(['branch_id' => $this->branch->id]);

        $response = $this->actingAs($this->viewer, 'sanctum')
            ->deleteJson("/api/v1/products/{$product->id}");

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_products(): void
    {
        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(401);
    }
}
