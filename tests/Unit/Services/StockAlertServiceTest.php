<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\StockAlert;
use App\Repositories\Contracts\StockAlertRepositoryInterface;
use App\Services\StockAlertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAlertServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StockAlertService $service;
    protected StockAlertRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(StockAlertRepositoryInterface::class);
        $this->service = new StockAlertService($this->repository);
    }

    public function test_can_get_all_stockAlerts(): void
    {
        StockAlert::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_stockAlerts(): void
    {
        StockAlert::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_stockAlert(): void
    {
        $data = StockAlert::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(StockAlert::class, $result);
        $this->assertDatabaseHas('stockAlerts', ['id' => $result->id]);
    }

    public function test_can_update_stockAlert(): void
    {
        $stockAlert = StockAlert::factory()->create();
        $updateData = StockAlert::factory()->make()->toArray();

        $result = $this->service->update($stockAlert->id, $updateData);

        $this->assertInstanceOf(StockAlert::class, $result);
    }

    public function test_can_delete_stockAlert(): void
    {
        $stockAlert = StockAlert::factory()->create();

        $result = $this->service->delete($stockAlert->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('stockAlerts', ['id' => $stockAlert->id]);
    }

    public function test_can_find_stockAlert_by_id(): void
    {
        $stockAlert = StockAlert::factory()->create();

        $result = $this->service->findById($stockAlert->id);

        $this->assertInstanceOf(StockAlert::class, $result);
        $this->assertEquals($stockAlert->id, $result->id);
    }
}
