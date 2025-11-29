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
        $this->superAdminRole = Role::create(['name' => 'Super Admin']);
        $this->receptionistRole = Role::create(['name' => 'Receptionist']);
        $this->viewerRole = Role::create(['name' => 'Viewer']);
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
