<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Branch;
use App\Models\Invoice;
use App\Models\User;
use App\Policies\InvoicePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InvoicePolicyTest extends TestCase
{
    use RefreshDatabase;

    private InvoicePolicy $policy;
    private Branch $branch;
    private Role $superAdminRole;
    private Role $accountantRole;
    private Role $viewerRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new InvoicePolicy();
        $this->branch = Branch::factory()->create();

        // Create roles with permissions
        $this->superAdminRole = Role::create(['name' => 'Super Admin']);
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'payments.view']));
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'payments.create']));

        $this->accountantRole = Role::create(['name' => 'Accountant']);
        $this->accountantRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'payments.view']));
        $this->accountantRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'payments.create']));

        $this->viewerRole = Role::create(['name' => 'Viewer']);
        $this->viewerRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'payments.view']));
    }

    public function test_accountant_can_view_any_invoices(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->accountantRole);

        $result = $this->policy->viewAny($user);

        $this->assertTrue($result);
    }

    public function test_user_can_view_invoice_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $invoice = Invoice::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->view($user, $invoice);

        $this->assertTrue($result);
    }

    public function test_user_cannot_view_invoice_from_different_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $invoice = Invoice::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->view($user, $invoice);

        $this->assertFalse($result);
    }

    public function test_accountant_can_create_invoice(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->accountantRole);

        $result = $this->policy->create($user);

        $this->assertTrue($result);
    }

    public function test_viewer_cannot_create_invoice(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $result = $this->policy->create($user);

        $this->assertFalse($result);
    }

    public function test_accountant_can_update_invoice_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->accountantRole);

        $invoice = Invoice::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->update($user, $invoice);

        $this->assertTrue($result);
    }

    public function test_super_admin_can_update_invoice_from_any_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->superAdminRole);

        $invoice = Invoice::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->update($user, $invoice);

        $this->assertTrue($result);
    }

    public function test_accountant_can_delete_invoice_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->accountantRole);

        $invoice = Invoice::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->delete($user, $invoice);

        $this->assertTrue($result);
    }
}
