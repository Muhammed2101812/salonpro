<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\AppointmentConflict;
use App\Repositories\Contracts\AppointmentConflictRepositoryInterface;
use App\Services\AppointmentConflictService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentConflictServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentConflictService $service;
    protected AppointmentConflictRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AppointmentConflictRepositoryInterface::class);
        $this->service = new AppointmentConflictService($this->repository);
    }

    public function test_can_get_all_appointmentConflicts(): void
    {
        AppointmentConflict::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_appointmentConflicts(): void
    {
        AppointmentConflict::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_appointmentConflict(): void
    {
        $data = AppointmentConflict::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(AppointmentConflict::class, $result);
        $this->assertDatabaseHas('appointmentConflicts', ['id' => $result->id]);
    }

    public function test_can_update_appointmentConflict(): void
    {
        $appointmentConflict = AppointmentConflict::factory()->create();
        $updateData = AppointmentConflict::factory()->make()->toArray();

        $result = $this->service->update($appointmentConflict->id, $updateData);

        $this->assertInstanceOf(AppointmentConflict::class, $result);
    }

    public function test_can_delete_appointmentConflict(): void
    {
        $appointmentConflict = AppointmentConflict::factory()->create();

        $result = $this->service->delete($appointmentConflict->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointmentConflicts', ['id' => $appointmentConflict->id]);
    }

    public function test_can_find_appointmentConflict_by_id(): void
    {
        $appointmentConflict = AppointmentConflict::factory()->create();

        $result = $this->service->findById($appointmentConflict->id);

        $this->assertInstanceOf(AppointmentConflict::class, $result);
        $this->assertEquals($appointmentConflict->id, $result->id);
    }
}
