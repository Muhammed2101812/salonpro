<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Branch;
use App\Models\Product;
use App\Models\User;
use App\Policies\ProductPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductPolicyTest extends TestCase
{
    use RefreshDatabase;

    private ProductPolicy $policy;
    private Branch $branch;
    private Role $superAdminRole;
    private Role $inventoryManagerRole;
    private Role $viewerRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new ProductPolicy();

        $this->branch = Branch::factory()->create();

        $this->superAdminRole = Role::create(['name' => 'Super Admin']);
        $this->inventoryManagerRole = Role::create(['name' => 'Inventory Manager']);
        $this->viewerRole = Role::create(['name' => 'Viewer']);
    }

    public function test_inventory_manager_can_view_any_products(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->inventoryManagerRole);

        $result = $this->policy->viewAny($user);

        $this->assertTrue($result);
    }

    public function test_user_can_view_product_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $product = Product::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->view($user, $product);

        $this->assertTrue($result);
    }

    public function test_user_cannot_view_product_from_different_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $product = Product::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->view($user, $product);

        $this->assertFalse($result);
    }

    public function test_inventory_manager_can_create_product(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->inventoryManagerRole);

        $result = $this->policy->create($user);

        $this->assertTrue($result);
    }

    public function test_viewer_cannot_create_product(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $result = $this->policy->create($user);

        $this->assertFalse($result);
    }

    public function test_inventory_manager_can_update_product_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->inventoryManagerRole);

        $product = Product::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->update($user, $product);

        $this->assertTrue($result);
    }

    public function test_inventory_manager_cannot_update_product_from_different_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->inventoryManagerRole);

        $product = Product::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->update($user, $product);

        $this->assertFalse($result);
    }

    public function test_super_admin_can_update_product_from_any_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->superAdminRole);

        $product = Product::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->update($user, $product);

        $this->assertTrue($result);
    }
}
