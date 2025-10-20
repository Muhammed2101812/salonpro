<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\AppointmentGroup;
use App\Repositories\Eloquent\AppointmentGroupRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentGroupRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentGroupRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AppointmentGroupRepository(new AppointmentGroup());
    }

    public function test_can_get_all_records(): void
    {
        AppointmentGroup::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = AppointmentGroup::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(AppointmentGroup::class, $result);
        $this->assertDatabaseHas('appointmentGroups', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $appointmentGroup = AppointmentGroup::factory()->create();

        $result = $this->repository->find($appointmentGroup->id);

        $this->assertInstanceOf(AppointmentGroup::class, $result);
        $this->assertEquals($appointmentGroup->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $appointmentGroup = AppointmentGroup::factory()->create();
        $updateData = AppointmentGroup::factory()->make()->toArray();

        $result = $this->repository->update($appointmentGroup->id, $updateData);

        $this->assertInstanceOf(AppointmentGroup::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $appointmentGroup = AppointmentGroup::factory()->create();

        $result = $this->repository->delete($appointmentGroup->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointmentGroups', ['id' => $appointmentGroup->id]);
    }
}
