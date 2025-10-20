<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\AppointmentReminder;
use App\Repositories\Eloquent\AppointmentReminderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentReminderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentReminderRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AppointmentReminderRepository(new AppointmentReminder());
    }

    public function test_can_get_all_records(): void
    {
        AppointmentReminder::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = AppointmentReminder::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(AppointmentReminder::class, $result);
        $this->assertDatabaseHas('appointmentReminders', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $appointmentReminder = AppointmentReminder::factory()->create();

        $result = $this->repository->find($appointmentReminder->id);

        $this->assertInstanceOf(AppointmentReminder::class, $result);
        $this->assertEquals($appointmentReminder->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $appointmentReminder = AppointmentReminder::factory()->create();
        $updateData = AppointmentReminder::factory()->make()->toArray();

        $result = $this->repository->update($appointmentReminder->id, $updateData);

        $this->assertInstanceOf(AppointmentReminder::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $appointmentReminder = AppointmentReminder::factory()->create();

        $result = $this->repository->delete($appointmentReminder->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointmentReminders', ['id' => $appointmentReminder->id]);
    }
}
