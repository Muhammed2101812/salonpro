<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin role
        $adminRole = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'appointments.view']));
        $adminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'appointments.create']));
        $adminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'appointments.update']));
        $adminRole->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'appointments.delete']));

        $branch = \App\Models\Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $branch->id]);
        $this->user->assignRole('admin');
    }

    public function test_can_list_appointments(): void
    {
        Appointment::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/appointments');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_appointment(): void
    {
        $data = Appointment::factory()->make()->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/appointments', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_show_appointment(): void
    {
        $appointment = Appointment::factory()->create(['branch_id' => $this->user->branch_id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/appointments/{$appointment->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_appointment(): void
    {
        $appointment = Appointment::factory()->create(['branch_id' => $this->user->branch_id]);
        $updateData = Appointment::factory()->make(['branch_id' => $this->user->branch_id])->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/appointments/{$appointment->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_appointment(): void
    {
        $appointment = Appointment::factory()->create(['branch_id' => $this->user->branch_id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/appointments/{$appointment->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertSoftDeleted('appointments', [
            'id' => $appointment->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        $response = $this->getJson('/api/v1/appointments');

        $response->assertUnauthorized();
    }
}
