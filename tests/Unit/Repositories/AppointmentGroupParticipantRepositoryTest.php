<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\AppointmentGroupParticipant;
use App\Repositories\Eloquent\AppointmentGroupParticipantRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentGroupParticipantRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentGroupParticipantRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AppointmentGroupParticipantRepository(new AppointmentGroupParticipant());
    }

    public function test_can_get_all_records(): void
    {
        AppointmentGroupParticipant::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = AppointmentGroupParticipant::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(AppointmentGroupParticipant::class, $result);
        $this->assertDatabaseHas('appointmentGroupParticipants', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $appointmentGroupParticipant = AppointmentGroupParticipant::factory()->create();

        $result = $this->repository->find($appointmentGroupParticipant->id);

        $this->assertInstanceOf(AppointmentGroupParticipant::class, $result);
        $this->assertEquals($appointmentGroupParticipant->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $appointmentGroupParticipant = AppointmentGroupParticipant::factory()->create();
        $updateData = AppointmentGroupParticipant::factory()->make()->toArray();

        $result = $this->repository->update($appointmentGroupParticipant->id, $updateData);

        $this->assertInstanceOf(AppointmentGroupParticipant::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $appointmentGroupParticipant = AppointmentGroupParticipant::factory()->create();

        $result = $this->repository->delete($appointmentGroupParticipant->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointmentGroupParticipants', ['id' => $appointmentGroupParticipant->id]);
    }
}
