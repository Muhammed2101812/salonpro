<?php

declare(strict_types=1);

namespace Tests\Feature\API;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AppointmentAPITest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_appointments(): void
    {
        Appointment::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/appointments');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_appointment(): void
    {
        $branch = Branch::factory()->create();
        $customer = Customer::factory()->create(['branch_id' => $branch->id]);
        $employee = Employee::factory()->create(['branch_id' => $branch->id]);
        $service = Service::factory()->create();

        $appointmentData = [
            'branch_id' => $branch->id,
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'service_id' => $service->id,
            'appointment_date' => '2025-11-20 14:00:00',
            'duration_minutes' => 60,
            'price' => 150.00,
            'status' => 'pending',
        ];

        $response = $this->postJson('/api/v1/appointments', $appointmentData);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('appointments', [
            'customer_id' => $customer->id,
            'status' => 'pending',
        ]);
    }

    public function test_can_show_appointment(): void
    {
        $appointment = Appointment::factory()->create();

        $response = $this->getJson("/api/v1/appointments/{$appointment->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $appointment->id)
            ->assertJsonPath('data.status', $appointment->status);
    }

    public function test_can_update_appointment(): void
    {
        $appointment = Appointment::factory()->create(['status' => 'pending']);

        $updateData = [
            'status' => 'confirmed',
        ];

        $response = $this->putJson("/api/v1/appointments/{$appointment->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'confirmed');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_can_delete_appointment(): void
    {
        $appointment = Appointment::factory()->create();

        $response = $this->deleteJson("/api/v1/appointments/{$appointment->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('appointments', ['id' => $appointment->id]);
    }

    public function test_validation_fails_for_invalid_status(): void
    {
        $appointment = Appointment::factory()->create();

        $invalidData = [
            'status' => 'invalid_status',
        ];

        $response = $this->putJson("/api/v1/appointments/{$appointment->id}", $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_requires_authentication(): void
    {
        Sanctum::actingAs(null);

        $response = $this->getJson('/api/v1/appointments');

        $response->assertStatus(401);
    }
}
