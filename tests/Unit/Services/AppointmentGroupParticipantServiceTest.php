<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\AppointmentGroupParticipant;
use App\Repositories\Contracts\AppointmentGroupParticipantRepositoryInterface;
use App\Services\AppointmentGroupParticipantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentGroupParticipantServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentGroupParticipantService $service;
    protected AppointmentGroupParticipantRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AppointmentGroupParticipantRepositoryInterface::class);
        $this->service = new AppointmentGroupParticipantService($this->repository);
    }

    public function test_can_get_all_appointmentGroupParticipants(): void
    {
        AppointmentGroupParticipant::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_appointmentGroupParticipants(): void
    {
        AppointmentGroupParticipant::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_appointmentGroupParticipant(): void
    {
        $data = AppointmentGroupParticipant::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(AppointmentGroupParticipant::class, $result);
        $this->assertDatabaseHas('appointmentGroupParticipants', ['id' => $result->id]);
    }

    public function test_can_update_appointmentGroupParticipant(): void
    {
        $appointmentGroupParticipant = AppointmentGroupParticipant::factory()->create();
        $updateData = AppointmentGroupParticipant::factory()->make()->toArray();

        $result = $this->service->update($appointmentGroupParticipant->id, $updateData);

        $this->assertInstanceOf(AppointmentGroupParticipant::class, $result);
    }

    public function test_can_delete_appointmentGroupParticipant(): void
    {
        $appointmentGroupParticipant = AppointmentGroupParticipant::factory()->create();

        $result = $this->service->delete($appointmentGroupParticipant->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointmentGroupParticipants', ['id' => $appointmentGroupParticipant->id]);
    }

    public function test_can_find_appointmentGroupParticipant_by_id(): void
    {
        $appointmentGroupParticipant = AppointmentGroupParticipant::factory()->create();

        $result = $this->service->findById($appointmentGroupParticipant->id);

        $this->assertInstanceOf(AppointmentGroupParticipant::class, $result);
        $this->assertEquals($appointmentGroupParticipant->id, $result->id);
    }
}
