<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\AppointmentCancellation;
use App\Repositories\Eloquent\AppointmentCancellationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentCancellationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentCancellationRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AppointmentCancellationRepository(new AppointmentCancellation());
    }

    public function test_can_get_all_records(): void
    {
        AppointmentCancellation::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = AppointmentCancellation::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(AppointmentCancellation::class, $result);
        $this->assertDatabaseHas('appointmentCancellations', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $appointmentCancellation = AppointmentCancellation::factory()->create();

        $result = $this->repository->find($appointmentCancellation->id);

        $this->assertInstanceOf(AppointmentCancellation::class, $result);
        $this->assertEquals($appointmentCancellation->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $appointmentCancellation = AppointmentCancellation::factory()->create();
        $updateData = AppointmentCancellation::factory()->make()->toArray();

        $result = $this->repository->update($appointmentCancellation->id, $updateData);

        $this->assertInstanceOf(AppointmentCancellation::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $appointmentCancellation = AppointmentCancellation::factory()->create();

        $result = $this->repository->delete($appointmentCancellation->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointmentCancellations', ['id' => $appointmentCancellation->id]);
    }
}
