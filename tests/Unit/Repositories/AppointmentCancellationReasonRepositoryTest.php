<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\AppointmentCancellationReason;
use App\Repositories\Eloquent\AppointmentCancellationReasonRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentCancellationReasonRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentCancellationReasonRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AppointmentCancellationReasonRepository(new AppointmentCancellationReason());
    }

    public function test_can_get_all_records(): void
    {
        AppointmentCancellationReason::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = AppointmentCancellationReason::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(AppointmentCancellationReason::class, $result);
        $this->assertDatabaseHas('appointmentCancellationReasons', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $appointmentCancellationReason = AppointmentCancellationReason::factory()->create();

        $result = $this->repository->find($appointmentCancellationReason->id);

        $this->assertInstanceOf(AppointmentCancellationReason::class, $result);
        $this->assertEquals($appointmentCancellationReason->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $appointmentCancellationReason = AppointmentCancellationReason::factory()->create();
        $updateData = AppointmentCancellationReason::factory()->make()->toArray();

        $result = $this->repository->update($appointmentCancellationReason->id, $updateData);

        $this->assertInstanceOf(AppointmentCancellationReason::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $appointmentCancellationReason = AppointmentCancellationReason::factory()->create();

        $result = $this->repository->delete($appointmentCancellationReason->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointmentCancellationReasons', ['id' => $appointmentCancellationReason->id]);
    }
}
