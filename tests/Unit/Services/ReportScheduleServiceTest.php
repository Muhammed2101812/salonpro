<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ReportSchedule;
use App\Repositories\Contracts\ReportScheduleRepositoryInterface;
use App\Services\ReportScheduleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportScheduleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ReportScheduleService $service;
    protected ReportScheduleRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ReportScheduleRepositoryInterface::class);
        $this->service = new ReportScheduleService($this->repository);
    }

    public function test_can_get_all_reportSchedules(): void
    {
        ReportSchedule::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_reportSchedules(): void
    {
        ReportSchedule::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_reportSchedule(): void
    {
        $data = ReportSchedule::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ReportSchedule::class, $result);
        $this->assertDatabaseHas('reportSchedules', ['id' => $result->id]);
    }

    public function test_can_update_reportSchedule(): void
    {
        $reportSchedule = ReportSchedule::factory()->create();
        $updateData = ReportSchedule::factory()->make()->toArray();

        $result = $this->service->update($reportSchedule->id, $updateData);

        $this->assertInstanceOf(ReportSchedule::class, $result);
    }

    public function test_can_delete_reportSchedule(): void
    {
        $reportSchedule = ReportSchedule::factory()->create();

        $result = $this->service->delete($reportSchedule->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('reportSchedules', ['id' => $reportSchedule->id]);
    }

    public function test_can_find_reportSchedule_by_id(): void
    {
        $reportSchedule = ReportSchedule::factory()->create();

        $result = $this->service->findById($reportSchedule->id);

        $this->assertInstanceOf(ReportSchedule::class, $result);
        $this->assertEquals($reportSchedule->id, $result->id);
    }
}
