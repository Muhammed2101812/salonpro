<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\AppointmentHistory;
use App\Repositories\Contracts\AppointmentHistoryRepositoryInterface;
use App\Services\AppointmentHistoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentHistoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentHistoryService $service;
    protected AppointmentHistoryRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AppointmentHistoryRepositoryInterface::class);
        $this->service = new AppointmentHistoryService($this->repository);
    }

    public function test_can_get_all_appointmentHistorys(): void
    {
        AppointmentHistory::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_appointmentHistorys(): void
    {
        AppointmentHistory::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_appointmentHistory(): void
    {
        $data = AppointmentHistory::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(AppointmentHistory::class, $result);
        $this->assertDatabaseHas('appointmentHistorys', ['id' => $result->id]);
    }

    public function test_can_update_appointmentHistory(): void
    {
        $appointmentHistory = AppointmentHistory::factory()->create();
        $updateData = AppointmentHistory::factory()->make()->toArray();

        $result = $this->service->update($appointmentHistory->id, $updateData);

        $this->assertInstanceOf(AppointmentHistory::class, $result);
    }

    public function test_can_delete_appointmentHistory(): void
    {
        $appointmentHistory = AppointmentHistory::factory()->create();

        $result = $this->service->delete($appointmentHistory->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('appointmentHistorys', ['id' => $appointmentHistory->id]);
    }

    public function test_can_find_appointmentHistory_by_id(): void
    {
        $appointmentHistory = AppointmentHistory::factory()->create();

        $result = $this->service->findById($appointmentHistory->id);

        $this->assertInstanceOf(AppointmentHistory::class, $result);
        $this->assertEquals($appointmentHistory->id, $result->id);
    }
}
