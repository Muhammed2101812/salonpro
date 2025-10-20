<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\DashboardWidget;
use App\Repositories\Contracts\DashboardWidgetRepositoryInterface;
use App\Services\DashboardWidgetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardWidgetServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DashboardWidgetService $service;
    protected DashboardWidgetRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(DashboardWidgetRepositoryInterface::class);
        $this->service = new DashboardWidgetService($this->repository);
    }

    public function test_can_get_all_dashboardWidgets(): void
    {
        DashboardWidget::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_dashboardWidgets(): void
    {
        DashboardWidget::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_dashboardWidget(): void
    {
        $data = DashboardWidget::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(DashboardWidget::class, $result);
        $this->assertDatabaseHas('dashboardWidgets', ['id' => $result->id]);
    }

    public function test_can_update_dashboardWidget(): void
    {
        $dashboardWidget = DashboardWidget::factory()->create();
        $updateData = DashboardWidget::factory()->make()->toArray();

        $result = $this->service->update($dashboardWidget->id, $updateData);

        $this->assertInstanceOf(DashboardWidget::class, $result);
    }

    public function test_can_delete_dashboardWidget(): void
    {
        $dashboardWidget = DashboardWidget::factory()->create();

        $result = $this->service->delete($dashboardWidget->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('dashboardWidgets', ['id' => $dashboardWidget->id]);
    }

    public function test_can_find_dashboardWidget_by_id(): void
    {
        $dashboardWidget = DashboardWidget::factory()->create();

        $result = $this->service->findById($dashboardWidget->id);

        $this->assertInstanceOf(DashboardWidget::class, $result);
        $this->assertEquals($dashboardWidget->id, $result->id);
    }
}
