<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\AppointmentReminder;
use App\Repositories\Contracts\AppointmentReminderRepositoryInterface;
use App\Services\AppointmentReminderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentReminderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentReminderService $service;
    protected AppointmentReminderRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AppointmentReminderRepositoryInterface::class);
        $this->service = new AppointmentReminderService($this->repository);
    }

    public function test_can_get_all_appointmentReminders(): void
    {
        AppointmentReminder::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_appointmentReminders(): void
    {
        AppointmentReminder::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_appointmentReminder(): void
    {
        $data = AppointmentReminder::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(AppointmentReminder::class, $result);
        $this->assertDatabaseHas('appointmentReminders', ['id' => $result->id]);
    }

    public function test_can_update_appointmentReminder(): void
    {
        $appointmentReminder = AppointmentReminder::factory()->create();
        $updateData = AppointmentReminder::factory()->make()->toArray();

        $result = $this->service->update($appointmentReminder->id, $updateData);

        $this->assertInstanceOf(AppointmentReminder::class, $result);
    }

    public function test_can_delete_appointmentReminder(): void
    {
        $appointmentReminder = AppointmentReminder::factory()->create();

        $result = $this->service->delete($appointmentReminder->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointmentReminders', ['id' => $appointmentReminder->id]);
    }

    public function test_can_find_appointmentReminder_by_id(): void
    {
        $appointmentReminder = AppointmentReminder::factory()->create();

        $result = $this->service->findById($appointmentReminder->id);

        $this->assertInstanceOf(AppointmentReminder::class, $result);
        $this->assertEquals($appointmentReminder->id, $result->id);
    }
}
