<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use App\Policies\EmployeePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EmployeePolicyTest extends TestCase
{
    use RefreshDatabase;

    private EmployeePolicy $policy;
    private Branch $branch;
    private Role $superAdminRole;
    private Role $hrManagerRole;
    private Role $viewerRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new EmployeePolicy();
        $this->branch = Branch::factory()->create();

        // Create roles with permissions
        $this->superAdminRole = Role::create(['name' => 'Super Admin']);
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'employees.view']));
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'employees.create']));
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'employees.update']));
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'employees.delete']));
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'employees.manage-schedule']));
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'employees.view-performance']));

        $this->hrManagerRole = Role::create(['name' => 'HR Manager']);
        $this->hrManagerRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'employees.view']));
        $this->hrManagerRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'employees.create']));
        $this->hrManagerRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'employees.update']));
        $this->hrManagerRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'employees.delete']));

        $this->viewerRole = Role::create(['name' => 'Viewer']);
        $this->viewerRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'employees.view']));
    }

    public function test_hr_manager_can_view_any_employees(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->hrManagerRole);

        $result = $this->policy->viewAny($user);

        $this->assertTrue($result);
    }

    public function test_user_can_view_employee_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $employee = Employee::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->view($user, $employee);

        $this->assertTrue($result);
    }

    public function test_user_cannot_view_employee_from_different_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $employee = Employee::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->view($user, $employee);

        $this->assertFalse($result);
    }

    public function test_hr_manager_can_create_employee(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->hrManagerRole);

        $result = $this->policy->create($user);

        $this->assertTrue($result);
    }

    public function test_viewer_cannot_create_employee(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $result = $this->policy->create($user);

        $this->assertFalse($result);
    }

    public function test_hr_manager_can_update_employee_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->hrManagerRole);

        $employee = Employee::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->update($user, $employee);

        $this->assertTrue($result);
    }

    public function test_super_admin_can_update_employee_from_any_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->superAdminRole);

        $employee = Employee::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->update($user, $employee);

        $this->assertTrue($result);
    }

    public function test_hr_manager_can_delete_employee_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->hrManagerRole);

        $employee = Employee::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->delete($user, $employee);

        $this->assertTrue($result);
    }
}
