<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\User;
use App\Policies\CustomerPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerPolicyTest extends TestCase
{
    use RefreshDatabase;

    private CustomerPolicy $policy;
    private Branch $branch;
    private Role $superAdminRole;
    private Role $branchManagerRole;
    private Role $viewerRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new CustomerPolicy();

        // Create branch
        $this->branch = Branch::factory()->create();

        // Create roles
        $this->superAdminRole = Role::create(['name' => 'Super Admin']);
        $this->branchManagerRole = Role::create(['name' => 'Branch Manager']);
        $this->viewerRole = Role::create(['name' => 'Viewer']);
    }

    public function test_super_admin_can_view_any_customers(): void
    {
        $user = User::factory()->create();
        $user->assignRole($this->superAdminRole);

        $result = $this->policy->viewAny($user);

        $this->assertTrue($result);
    }

    public function test_branch_manager_can_view_customers(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->branchManagerRole);

        $result = $this->policy->viewAny($user);

        $this->assertTrue($result);
    }

    public function test_user_can_view_customer_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->view($user, $customer);

        $this->assertTrue($result);
    }

    public function test_user_cannot_view_customer_from_different_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $customer = Customer::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->view($user, $customer);

        $this->assertFalse($result);
    }

    public function test_super_admin_can_view_customer_from_any_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->superAdminRole);

        $customer = Customer::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->view($user, $customer);

        $this->assertTrue($result);
    }

    public function test_user_can_create_customer(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->branchManagerRole);

        $result = $this->policy->create($user);

        $this->assertTrue($result);
    }

    public function test_user_can_update_customer_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->branchManagerRole);

        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->update($user, $customer);

        $this->assertTrue($result);
    }

    public function test_user_cannot_update_customer_from_different_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->branchManagerRole);

        $customer = Customer::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->update($user, $customer);

        $this->assertFalse($result);
    }

    public function test_user_can_delete_customer_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->branchManagerRole);

        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->delete($user, $customer);

        $this->assertTrue($result);
    }

    public function test_viewer_cannot_delete_customer(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->delete($user, $customer);

        $this->assertFalse($result);
    }
}
