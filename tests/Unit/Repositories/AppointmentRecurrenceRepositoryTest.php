<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\AppointmentRecurrence;
use App\Repositories\Eloquent\AppointmentRecurrenceRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentRecurrenceRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentRecurrenceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AppointmentRecurrenceRepository(new AppointmentRecurrence());
    }

    public function test_can_get_all_records(): void
    {
        AppointmentRecurrence::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = AppointmentRecurrence::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(AppointmentRecurrence::class, $result);
        $this->assertDatabaseHas('appointmentRecurrences', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $appointmentRecurrence = AppointmentRecurrence::factory()->create();

        $result = $this->repository->find($appointmentRecurrence->id);

        $this->assertInstanceOf(AppointmentRecurrence::class, $result);
        $this->assertEquals($appointmentRecurrence->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $appointmentRecurrence = AppointmentRecurrence::factory()->create();
        $updateData = AppointmentRecurrence::factory()->make()->toArray();

        $result = $this->repository->update($appointmentRecurrence->id, $updateData);

        $this->assertInstanceOf(AppointmentRecurrence::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $appointmentRecurrence = AppointmentRecurrence::factory()->create();

        $result = $this->repository->delete($appointmentRecurrence->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointmentRecurrences', ['id' => $appointmentRecurrence->id]);
    }
}
