<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\AppointmentConflict;
use App\Repositories\Eloquent\AppointmentConflictRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentConflictRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentConflictRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AppointmentConflictRepository(new AppointmentConflict());
    }

    public function test_can_get_all_records(): void
    {
        AppointmentConflict::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = AppointmentConflict::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(AppointmentConflict::class, $result);
        $this->assertDatabaseHas('appointmentConflicts', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $appointmentConflict = AppointmentConflict::factory()->create();

        $result = $this->repository->find($appointmentConflict->id);

        $this->assertInstanceOf(AppointmentConflict::class, $result);
        $this->assertEquals($appointmentConflict->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $appointmentConflict = AppointmentConflict::factory()->create();
        $updateData = AppointmentConflict::factory()->make()->toArray();

        $result = $this->repository->update($appointmentConflict->id, $updateData);

        $this->assertInstanceOf(AppointmentConflict::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $appointmentConflict = AppointmentConflict::factory()->create();

        $result = $this->repository->delete($appointmentConflict->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointmentConflicts', ['id' => $appointmentConflict->id]);
    }
}
