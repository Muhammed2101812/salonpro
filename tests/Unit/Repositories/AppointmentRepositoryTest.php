<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Appointment;
use App\Repositories\Eloquent\AppointmentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AppointmentRepository(new Appointment());
    }

    public function test_can_get_all_records(): void
    {
        Appointment::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = Appointment::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(Appointment::class, $result);
        $this->assertDatabaseHas('appointments', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $appointment = Appointment::factory()->create();

        $result = $this->repository->find($appointment->id);

        $this->assertInstanceOf(Appointment::class, $result);
        $this->assertEquals($appointment->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $appointment = Appointment::factory()->create();
        $updateData = Appointment::factory()->make()->toArray();

        $result = $this->repository->update($appointment->id, $updateData);

        $this->assertInstanceOf(Appointment::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $appointment = Appointment::factory()->create();

        $result = $this->repository->delete($appointment->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointments', ['id' => $appointment->id]);
    }
}
