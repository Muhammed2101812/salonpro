<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\AppointmentWaitlist;
use App\Repositories\Contracts\AppointmentWaitlistRepositoryInterface;
use App\Services\AppointmentWaitlistService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentWaitlistServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentWaitlistService $service;
    protected AppointmentWaitlistRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AppointmentWaitlistRepositoryInterface::class);
        $this->service = new AppointmentWaitlistService($this->repository);
    }

    public function test_can_get_all_appointmentWaitlists(): void
    {
        AppointmentWaitlist::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_appointmentWaitlists(): void
    {
        AppointmentWaitlist::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_appointmentWaitlist(): void
    {
        $data = AppointmentWaitlist::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(AppointmentWaitlist::class, $result);
        $this->assertDatabaseHas('appointmentWaitlists', ['id' => $result->id]);
    }

    public function test_can_update_appointmentWaitlist(): void
    {
        $appointmentWaitlist = AppointmentWaitlist::factory()->create();
        $updateData = AppointmentWaitlist::factory()->make()->toArray();

        $result = $this->service->update($appointmentWaitlist->id, $updateData);

        $this->assertInstanceOf(AppointmentWaitlist::class, $result);
    }

    public function test_can_delete_appointmentWaitlist(): void
    {
        $appointmentWaitlist = AppointmentWaitlist::factory()->create();

        $result = $this->service->delete($appointmentWaitlist->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointmentWaitlists', ['id' => $appointmentWaitlist->id]);
    }

    public function test_can_find_appointmentWaitlist_by_id(): void
    {
        $appointmentWaitlist = AppointmentWaitlist::factory()->create();

        $result = $this->service->findById($appointmentWaitlist->id);

        $this->assertInstanceOf(AppointmentWaitlist::class, $result);
        $this->assertEquals($appointmentWaitlist->id, $result->id);
    }
}
