<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ReportExecution;
use App\Repositories\Contracts\ReportExecutionRepositoryInterface;
use App\Services\ReportExecutionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExecutionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ReportExecutionService $service;
    protected ReportExecutionRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ReportExecutionRepositoryInterface::class);
        $this->service = new ReportExecutionService($this->repository);
    }

    public function test_can_get_all_reportExecutions(): void
    {
        ReportExecution::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_reportExecutions(): void
    {
        ReportExecution::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_reportExecution(): void
    {
        $data = ReportExecution::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ReportExecution::class, $result);
        $this->assertDatabaseHas('reportExecutions', ['id' => $result->id]);
    }

    public function test_can_update_reportExecution(): void
    {
        $reportExecution = ReportExecution::factory()->create();
        $updateData = ReportExecution::factory()->make()->toArray();

        $result = $this->service->update($reportExecution->id, $updateData);

        $this->assertInstanceOf(ReportExecution::class, $result);
    }

    public function test_can_delete_reportExecution(): void
    {
        $reportExecution = ReportExecution::factory()->create();

        $result = $this->service->delete($reportExecution->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('reportExecutions', ['id' => $reportExecution->id]);
    }

    public function test_can_find_reportExecution_by_id(): void
    {
        $reportExecution = ReportExecution::factory()->create();

        $result = $this->service->findById($reportExecution->id);

        $this->assertInstanceOf(ReportExecution::class, $result);
        $this->assertEquals($reportExecution->id, $result->id);
    }
}
