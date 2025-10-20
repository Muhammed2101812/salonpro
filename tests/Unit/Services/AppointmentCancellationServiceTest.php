<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\AppointmentCancellation;
use App\Repositories\Contracts\AppointmentCancellationRepositoryInterface;
use App\Services\AppointmentCancellationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentCancellationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentCancellationService $service;
    protected AppointmentCancellationRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AppointmentCancellationRepositoryInterface::class);
        $this->service = new AppointmentCancellationService($this->repository);
    }

    public function test_can_get_all_appointmentCancellations(): void
    {
        AppointmentCancellation::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_appointmentCancellations(): void
    {
        AppointmentCancellation::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_appointmentCancellation(): void
    {
        $data = AppointmentCancellation::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(AppointmentCancellation::class, $result);
        $this->assertDatabaseHas('appointmentCancellations', ['id' => $result->id]);
    }

    public function test_can_update_appointmentCancellation(): void
    {
        $appointmentCancellation = AppointmentCancellation::factory()->create();
        $updateData = AppointmentCancellation::factory()->make()->toArray();

        $result = $this->service->update($appointmentCancellation->id, $updateData);

        $this->assertInstanceOf(AppointmentCancellation::class, $result);
    }

    public function test_can_delete_appointmentCancellation(): void
    {
        $appointmentCancellation = AppointmentCancellation::factory()->create();

        $result = $this->service->delete($appointmentCancellation->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointmentCancellations', ['id' => $appointmentCancellation->id]);
    }

    public function test_can_find_appointmentCancellation_by_id(): void
    {
        $appointmentCancellation = AppointmentCancellation::factory()->create();

        $result = $this->service->findById($appointmentCancellation->id);

        $this->assertInstanceOf(AppointmentCancellation::class, $result);
        $this->assertEquals($appointmentCancellation->id, $result->id);
    }
}
