<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\User;
use App\Policies\AppointmentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AppointmentPolicyTest extends TestCase
{
    use RefreshDatabase;

    private AppointmentPolicy $policy;
    private Branch $branch;
    private Role $superAdminRole;
    private Role $receptionistRole;
    private Role $viewerRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new AppointmentPolicy();
        $this->branch = Branch::factory()->create();

        // Create roles with permissions
        $this->superAdminRole = Role::create(['name' => 'Super Admin']);
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'appointments.view']));
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'appointments.view-all']));
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'appointments.create']));
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'appointments.update']));
        $this->superAdminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'appointments.delete']));

        $this->receptionistRole = Role::create(['name' => 'Receptionist']);
        $this->receptionistRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'appointments.view']));
        $this->receptionistRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'appointments.create']));
        $this->receptionistRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'appointments.update']));
        $this->receptionistRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'appointments.delete']));

        $this->viewerRole = Role::create(['name' => 'Viewer']);
        $this->viewerRole->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'appointments.view']));
    }

    public function test_receptionist_can_view_any_appointments(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->receptionistRole);

        $result = $this->policy->viewAny($user);

        $this->assertTrue($result);
    }

    public function test_user_can_view_appointment_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $appointment = Appointment::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->view($user, $appointment);

        $this->assertTrue($result);
    }

    public function test_user_cannot_view_appointment_from_different_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $appointment = Appointment::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->view($user, $appointment);

        $this->assertFalse($result);
    }

    public function test_super_admin_can_view_appointment_from_any_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->superAdminRole);

        $appointment = Appointment::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->view($user, $appointment);

        $this->assertTrue($result);
    }

    public function test_receptionist_can_create_appointment(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->receptionistRole);

        $result = $this->policy->create($user);

        $this->assertTrue($result);
    }

    public function test_viewer_cannot_create_appointment(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $result = $this->policy->create($user);

        $this->assertFalse($result);
    }

    public function test_receptionist_can_update_appointment_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->receptionistRole);

        $appointment = Appointment::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->update($user, $appointment);

        $this->assertTrue($result);
    }

    public function test_receptionist_cannot_update_appointment_from_different_branch(): void
    {
        $branch2 = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->receptionistRole);

        $appointment = Appointment::factory()->create(['branch_id' => $branch2->id]);

        $result = $this->policy->update($user, $appointment);

        $this->assertFalse($result);
    }

    public function test_receptionist_can_delete_appointment_from_same_branch(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->receptionistRole);

        $appointment = Appointment::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->delete($user, $appointment);

        $this->assertTrue($result);
    }

    public function test_viewer_cannot_delete_appointment(): void
    {
        $user = User::factory()->create(['branch_id' => $this->branch->id]);
        $user->assignRole($this->viewerRole);

        $appointment = Appointment::factory()->create(['branch_id' => $this->branch->id]);

        $result = $this->policy->delete($user, $appointment);

        $this->assertFalse($result);
    }
}
