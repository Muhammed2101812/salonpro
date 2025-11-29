<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_appointment_can_be_created(): void
    {
        $branch = Branch::factory()->create();
        $customer = Customer::factory()->create(['branch_id' => $branch->id]);
        $employee = Employee::factory()->create(['branch_id' => $branch->id]);
        $service = Service::factory()->create();

        $appointment = Appointment::create([
            'branch_id' => $branch->id,
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'service_id' => $service->id,
            'appointment_date' => '2025-11-20 14:00:00',
            'duration_minutes' => 60,
            'price' => 150.00,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('appointments', [
            'customer_id' => $customer->id,
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(Appointment::class, $appointment);
    }

    public function test_appointment_belongs_to_customer(): void
    {
        $customer = Customer::factory()->create();
        $appointment = Appointment::factory()->create(['customer_id' => $customer->id]);

        $this->assertInstanceOf(Customer::class, $appointment->customer);
        $this->assertEquals($customer->id, $appointment->customer->id);
    }

    public function test_appointment_belongs_to_employee(): void
    {
        $employee = Employee::factory()->create();
        $appointment = Appointment::factory()->create(['employee_id' => $employee->id]);

        $this->assertInstanceOf(Employee::class, $appointment->employee);
        $this->assertEquals($employee->id, $appointment->employee->id);
    }

    public function test_appointment_belongs_to_service(): void
    {
        $service = Service::factory()->create();
        $appointment = Appointment::factory()->create(['service_id' => $service->id]);

        $this->assertInstanceOf(Service::class, $appointment->service);
        $this->assertEquals($service->id, $appointment->service->id);
    }

    public function test_appointment_has_uuid(): void
    {
        $appointment = Appointment::factory()->create();

        $this->assertNotNull($appointment->id);
        // Laravel uses UUID v7 (ordered UUIDs), so check general UUID format
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $appointment->id
        );
    }

    public function test_appointment_has_valid_status(): void
    {
        $pending = Appointment::factory()->create(['status' => 'pending']);
        $confirmed = Appointment::factory()->create(['status' => 'confirmed']);
        $completed = Appointment::factory()->create(['status' => 'completed']);
        $cancelled = Appointment::factory()->create(['status' => 'cancelled']);

        $this->assertEquals('pending', $pending->status);
        $this->assertEquals('confirmed', $confirmed->status);
        $this->assertEquals('completed', $completed->status);
        $this->assertEquals('cancelled', $cancelled->status);
    }

    public function test_appointment_soft_deletes(): void
    {
        $appointment = Appointment::factory()->create();
        $appointmentId = $appointment->id;

        $appointment->delete();

        $this->assertSoftDeleted('appointments', ['id' => $appointmentId]);
    }
}
