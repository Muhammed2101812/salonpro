<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\AppointmentCancellationReason;
use App\Repositories\Contracts\AppointmentCancellationReasonRepositoryInterface;
use App\Services\AppointmentCancellationReasonService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentCancellationReasonServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentCancellationReasonService $service;
    protected AppointmentCancellationReasonRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AppointmentCancellationReasonRepositoryInterface::class);
        $this->service = new AppointmentCancellationReasonService($this->repository);
    }

    public function test_can_get_all_appointmentCancellationReasons(): void
    {
        AppointmentCancellationReason::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_appointmentCancellationReasons(): void
    {
        AppointmentCancellationReason::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_appointmentCancellationReason(): void
    {
        $data = AppointmentCancellationReason::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(AppointmentCancellationReason::class, $result);
        $this->assertDatabaseHas('appointmentCancellationReasons', ['id' => $result->id]);
    }

    public function test_can_update_appointmentCancellationReason(): void
    {
        $appointmentCancellationReason = AppointmentCancellationReason::factory()->create();
        $updateData = AppointmentCancellationReason::factory()->make()->toArray();

        $result = $this->service->update($appointmentCancellationReason->id, $updateData);

        $this->assertInstanceOf(AppointmentCancellationReason::class, $result);
    }

    public function test_can_delete_appointmentCancellationReason(): void
    {
        $appointmentCancellationReason = AppointmentCancellationReason::factory()->create();

        $result = $this->service->delete($appointmentCancellationReason->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointmentCancellationReasons', ['id' => $appointmentCancellationReason->id]);
    }

    public function test_can_find_appointmentCancellationReason_by_id(): void
    {
        $appointmentCancellationReason = AppointmentCancellationReason::factory()->create();

        $result = $this->service->findById($appointmentCancellationReason->id);

        $this->assertInstanceOf(AppointmentCancellationReason::class, $result);
        $this->assertEquals($appointmentCancellationReason->id, $result->id);
    }
}
