<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Services\AppointmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentService $service;
    protected AppointmentRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AppointmentRepositoryInterface::class);
        $this->service = new AppointmentService($this->repository);
    }

    public function test_can_get_all_appointments(): void
    {
        Appointment::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_appointments(): void
    {
        Appointment::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_appointment(): void
    {
        $data = Appointment::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Appointment::class, $result);
        $this->assertDatabaseHas('appointments', ['id' => $result->id]);
    }

    public function test_can_update_appointment(): void
    {
        $appointment = Appointment::factory()->create();
        $updateData = Appointment::factory()->make()->toArray();

        $result = $this->service->update($appointment->id, $updateData);

        $this->assertInstanceOf(Appointment::class, $result);
    }

    public function test_can_delete_appointment(): void
    {
        $appointment = Appointment::factory()->create();

        $result = $this->service->delete($appointment->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointments', ['id' => $appointment->id]);
    }

    public function test_can_find_appointment_by_id(): void
    {
        $appointment = Appointment::factory()->create();

        $result = $this->service->findById($appointment->id);

        $this->assertInstanceOf(Appointment::class, $result);
        $this->assertEquals($appointment->id, $result->id);
    }
}
