<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Branch;
use App\Models\Payment;
use App\Models\User;
use App\Policies\PaymentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaymentPolicyTest extends TestCase
{
    use RefreshDatabase;

    private PaymentPolicy $policy;
    private Branch $branch;
    private Role $superAdminRole;
    private Role $accountantRole;
    private Role $viewerRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new PaymentPolicy();
        $this->branch = Branch::factory()->create();

        // Create roles with permissions
        $this->superAdminRole = Role::create(['name' => 'Super Admin']);
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'payments.view']));
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'payments.create']));
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'payments.refund']));
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'payments.view-reports']));

        $this->accountantRole = Role::create(['name' => 'Accountant']);
        $this->accountantRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'payments.view']));
        $this->accountantRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'payments.create']));

        $this->viewerRole = Role::create(['name' => 'Viewer']);
        $this->viewerRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'payments.view']));
    }

    public function test_accountant_can_view_any_payments(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->accountantRole);

        $result = $this->policy->viewAny($user);

        $this->assertTrue($result);
    }

    public function test_user_can_view_payment_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $payment = Payment::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->view($user, $payment);

        $this->assertTrue($result);
    }

    public function test_user_cannot_view_payment_from_different_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $payment = Payment::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->view($user, $payment);

        $this->assertFalse($result);
    }

    public function test_accountant_can_create_payment(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->accountantRole);

        $result = $this->policy->create($user);

        $this->assertTrue($result);
    }

    public function test_viewer_cannot_create_payment(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $result = $this->policy->create($user);

        $this->assertFalse($result);
    }

    public function test_accountant_can_update_payment_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->accountantRole);

        $payment = Payment::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->update($user, $payment);

        $this->assertTrue($result);
    }

    public function test_super_admin_can_update_payment_from_any_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->superAdminRole);

        $payment = Payment::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->update($user, $payment);

        $this->assertTrue($result);
    }

    public function test_accountant_can_delete_payment_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->accountantRole);

        $payment = Payment::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->delete($user, $payment);

        $this->assertTrue($result);
    }
}
