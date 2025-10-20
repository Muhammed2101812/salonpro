<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\AppointmentRecurrence;
use App\Repositories\Contracts\AppointmentRecurrenceRepositoryInterface;
use App\Services\AppointmentRecurrenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentRecurrenceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentRecurrenceService $service;
    protected AppointmentRecurrenceRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AppointmentRecurrenceRepositoryInterface::class);
        $this->service = new AppointmentRecurrenceService($this->repository);
    }

    public function test_can_get_all_appointmentRecurrences(): void
    {
        AppointmentRecurrence::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_appointmentRecurrences(): void
    {
        AppointmentRecurrence::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_appointmentRecurrence(): void
    {
        $data = AppointmentRecurrence::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(AppointmentRecurrence::class, $result);
        $this->assertDatabaseHas('appointmentRecurrences', ['id' => $result->id]);
    }

    public function test_can_update_appointmentRecurrence(): void
    {
        $appointmentRecurrence = AppointmentRecurrence::factory()->create();
        $updateData = AppointmentRecurrence::factory()->make()->toArray();

        $result = $this->service->update($appointmentRecurrence->id, $updateData);

        $this->assertInstanceOf(AppointmentRecurrence::class, $result);
    }

    public function test_can_delete_appointmentRecurrence(): void
    {
        $appointmentRecurrence = AppointmentRecurrence::factory()->create();

        $result = $this->service->delete($appointmentRecurrence->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointmentRecurrences', ['id' => $appointmentRecurrence->id]);
    }

    public function test_can_find_appointmentRecurrence_by_id(): void
    {
        $appointmentRecurrence = AppointmentRecurrence::factory()->create();

        $result = $this->service->findById($appointmentRecurrence->id);

        $this->assertInstanceOf(AppointmentRecurrence::class, $result);
        $this->assertEquals($appointmentRecurrence->id, $result->id);
    }
}
