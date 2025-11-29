<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Branch;
use App\Models\Service;
use App\Models\User;
use App\Policies\ServicePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ServicePolicyTest extends TestCase
{
    use RefreshDatabase;

    private ServicePolicy $policy;
    private Branch $branch;
    private Role $superAdminRole;
    private Role $branchManagerRole;
    private Role $viewerRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new ServicePolicy();
        $this->branch = Branch::factory()->create();

        // Create roles with permissions
        $this->superAdminRole = Role::create(['name' => 'Super Admin']);
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'services.view']));
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'services.create']));
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'services.update']));
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'services.delete']));
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'services.manage-categories']));
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'services.manage-pricing']));

        $this->branchManagerRole = Role::create(['name' => 'Branch Manager']);
        $this->branchManagerRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'services.view']));
        $this->branchManagerRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'services.create']));
        $this->branchManagerRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'services.update']));
        $this->branchManagerRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'services.delete']));

        $this->viewerRole = Role::create(['name' => 'Viewer']);
        $this->viewerRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'services.view']));
    }

    public function test_branch_manager_can_view_any_services(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->branchManagerRole);

        $result = $this->policy->viewAny($user);

        $this->assertTrue($result);
    }

    public function test_user_can_view_service_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $service = Service::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->view($user, $service);

        $this->assertTrue($result);
    }

    public function test_user_cannot_view_service_from_different_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $service = Service::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->view($user, $service);

        $this->assertFalse($result);
    }

    public function test_branch_manager_can_create_service(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->branchManagerRole);

        $result = $this->policy->create($user);

        $this->assertTrue($result);
    }

    public function test_viewer_cannot_create_service(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $result = $this->policy->create($user);

        $this->assertFalse($result);
    }

    public function test_branch_manager_can_update_service_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->branchManagerRole);

        $service = Service::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->update($user, $service);

        $this->assertTrue($result);
    }

    public function test_super_admin_can_update_service_from_any_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->superAdminRole);

        $service = Service::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->update($user, $service);

        $this->assertTrue($result);
    }

    public function test_branch_manager_can_delete_service_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->branchManagerRole);

        $service = Service::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->delete($user, $service);

        $this->assertTrue($result);
    }

    public function test_viewer_cannot_delete_service(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $service = Service::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->delete($user, $service);

        $this->assertFalse($result);
    }
}
