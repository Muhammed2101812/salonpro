<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\AppointmentGroup;
use App\Repositories\Contracts\AppointmentGroupRepositoryInterface;
use App\Services\AppointmentGroupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentGroupServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentGroupService $service;
    protected AppointmentGroupRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AppointmentGroupRepositoryInterface::class);
        $this->service = new AppointmentGroupService($this->repository);
    }

    public function test_can_get_all_appointmentGroups(): void
    {
        AppointmentGroup::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_appointmentGroups(): void
    {
        AppointmentGroup::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_appointmentGroup(): void
    {
        $data = AppointmentGroup::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(AppointmentGroup::class, $result);
        $this->assertDatabaseHas('appointmentGroups', ['id' => $result->id]);
    }

    public function test_can_update_appointmentGroup(): void
    {
        $appointmentGroup = AppointmentGroup::factory()->create();
        $updateData = AppointmentGroup::factory()->make()->toArray();

        $result = $this->service->update($appointmentGroup->id, $updateData);

        $this->assertInstanceOf(AppointmentGroup::class, $result);
    }

    public function test_can_delete_appointmentGroup(): void
    {
        $appointmentGroup = AppointmentGroup::factory()->create();

        $result = $this->service->delete($appointmentGroup->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointmentGroups', ['id' => $appointmentGroup->id]);
    }

    public function test_can_find_appointmentGroup_by_id(): void
    {
        $appointmentGroup = AppointmentGroup::factory()->create();

        $result = $this->service->findById($appointmentGroup->id);

        $this->assertInstanceOf(AppointmentGroup::class, $result);
        $this->assertEquals($appointmentGroup->id, $result->id);
    }
}
