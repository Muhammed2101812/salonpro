<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ReportTemplate;
use App\Repositories\Contracts\ReportTemplateRepositoryInterface;
use App\Services\ReportTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTemplateServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ReportTemplateService $service;
    protected ReportTemplateRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ReportTemplateRepositoryInterface::class);
        $this->service = new ReportTemplateService($this->repository);
    }

    public function test_can_get_all_reportTemplates(): void
    {
        ReportTemplate::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_reportTemplates(): void
    {
        ReportTemplate::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_reportTemplate(): void
    {
        $data = ReportTemplate::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ReportTemplate::class, $result);
        $this->assertDatabaseHas('reportTemplates', ['id' => $result->id]);
    }

    public function test_can_update_reportTemplate(): void
    {
        $reportTemplate = ReportTemplate::factory()->create();
        $updateData = ReportTemplate::factory()->make()->toArray();

        $result = $this->service->update($reportTemplate->id, $updateData);

        $this->assertInstanceOf(ReportTemplate::class, $result);
    }

    public function test_can_delete_reportTemplate(): void
    {
        $reportTemplate = ReportTemplate::factory()->create();

        $result = $this->service->delete($reportTemplate->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('reportTemplates', ['id' => $reportTemplate->id]);
    }

    public function test_can_find_reportTemplate_by_id(): void
    {
        $reportTemplate = ReportTemplate::factory()->create();

        $result = $this->service->findById($reportTemplate->id);

        $this->assertInstanceOf(ReportTemplate::class, $result);
        $this->assertEquals($reportTemplate->id, $result->id);
    }
}
