<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\AppointmentWaitlist;
use App\Repositories\Eloquent\AppointmentWaitlistRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentWaitlistRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentWaitlistRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AppointmentWaitlistRepository(new AppointmentWaitlist());
    }

    public function test_can_get_all_records(): void
    {
        AppointmentWaitlist::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = AppointmentWaitlist::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(AppointmentWaitlist::class, $result);
        $this->assertDatabaseHas('appointmentWaitlists', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $appointmentWaitlist = AppointmentWaitlist::factory()->create();

        $result = $this->repository->find($appointmentWaitlist->id);

        $this->assertInstanceOf(AppointmentWaitlist::class, $result);
        $this->assertEquals($appointmentWaitlist->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $appointmentWaitlist = AppointmentWaitlist::factory()->create();
        $updateData = AppointmentWaitlist::factory()->make()->toArray();

        $result = $this->repository->update($appointmentWaitlist->id, $updateData);

        $this->assertInstanceOf(AppointmentWaitlist::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $appointmentWaitlist = AppointmentWaitlist::factory()->create();

        $result = $this->repository->delete($appointmentWaitlist->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointmentWaitlists', ['id' => $appointmentWaitlist->id]);
    }
}
