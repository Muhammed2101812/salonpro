<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Branch;
use App\Models\Sale;
use App\Models\User;
use App\Policies\SalePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalePolicyTest extends TestCase
{
    use RefreshDatabase;

    private SalePolicy $policy;
    private Branch $branch;
    private Role $superAdminRole;
    private Role $salesRepRole;
    private Role $viewerRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new SalePolicy();
        $this->branch = Branch::factory()->create();
        $this->superAdminRole = Role::create(['name' => 'Super Admin']);
        $this->salesRepRole = Role::create(['name' => 'Sales Rep']);
        $this->viewerRole = Role::create(['name' => 'Viewer']);
    }

    public function test_sales_rep_can_view_any_sales(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->salesRepRole);

        $result = $this->policy->viewAny($user);

        $this->assertTrue($result);
    }

    public function test_user_can_view_sale_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $sale = Sale::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->view($user, $sale);

        $this->assertTrue($result);
    }

    public function test_user_cannot_view_sale_from_different_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $sale = Sale::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->view($user, $sale);

        $this->assertFalse($result);
    }

    public function test_sales_rep_can_create_sale(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->salesRepRole);

        $result = $this->policy->create($user);

        $this->assertTrue($result);
    }

    public function test_viewer_cannot_create_sale(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $result = $this->policy->create($user);

        $this->assertFalse($result);
    }

    public function test_sales_rep_can_update_sale_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->salesRepRole);

        $sale = Sale::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->update($user, $sale);

        $this->assertTrue($result);
    }

    public function test_super_admin_can_update_sale_from_any_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->superAdminRole);

        $sale = Sale::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->update($user, $sale);

        $this->assertTrue($result);
    }

    public function test_sales_rep_can_delete_sale_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->salesRepRole);

        $sale = Sale::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->delete($user, $sale);

        $this->assertTrue($result);
    }
}
