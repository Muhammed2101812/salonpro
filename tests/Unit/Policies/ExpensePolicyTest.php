<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\User;
use App\Policies\ExpensePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExpensePolicyTest extends TestCase
{
    use RefreshDatabase;

    private ExpensePolicy $policy;
    private Branch $branch;
    private Role $superAdminRole;
    private Role $accountantRole;
    private Role $viewerRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new ExpensePolicy();
        $this->branch = Branch::factory()->create();
        $this->superAdminRole = Role::create(['name' => 'Super Admin']);
        $this->accountantRole = Role::create(['name' => 'Accountant']);
        $this->viewerRole = Role::create(['name' => 'Viewer']);
    }

    public function test_accountant_can_view_any_expenses(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->accountantRole);

        $result = $this->policy->viewAny($user);

        $this->assertTrue($result);
    }

    public function test_user_can_view_expense_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $expense = Expense::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->view($user, $expense);

        $this->assertTrue($result);
    }

    public function test_user_cannot_view_expense_from_different_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $expense = Expense::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->view($user, $expense);

        $this->assertFalse($result);
    }

    public function test_accountant_can_create_expense(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->accountantRole);

        $result = $this->policy->create($user);

        $this->assertTrue($result);
    }

    public function test_viewer_cannot_create_expense(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $result = $this->policy->create($user);

        $this->assertFalse($result);
    }

    public function test_accountant_can_update_expense_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->accountantRole);

        $expense = Expense::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->update($user, $expense);

        $this->assertTrue($result);
    }

    public function test_super_admin_can_update_expense_from_any_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->superAdminRole);

        $expense = Expense::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->update($user, $expense);

        $this->assertTrue($result);
    }

    public function test_accountant_can_delete_expense_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->accountantRole);

        $expense = Expense::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->delete($user, $expense);

        $this->assertTrue($result);
    }
}
