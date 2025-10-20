<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ProductStockHistory;
use App\Repositories\Contracts\ProductStockHistoryRepositoryInterface;
use App\Services\ProductStockHistoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductStockHistoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductStockHistoryService $service;
    protected ProductStockHistoryRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ProductStockHistoryRepositoryInterface::class);
        $this->service = new ProductStockHistoryService($this->repository);
    }

    public function test_can_get_all_productStockHistorys(): void
    {
        ProductStockHistory::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_productStockHistorys(): void
    {
        ProductStockHistory::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_productStockHistory(): void
    {
        $data = ProductStockHistory::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ProductStockHistory::class, $result);
        $this->assertDatabaseHas('productStockHistorys', ['id' => $result->id]);
    }

    public function test_can_update_productStockHistory(): void
    {
        $productStockHistory = ProductStockHistory::factory()->create();
        $updateData = ProductStockHistory::factory()->make()->toArray();

        $result = $this->service->update($productStockHistory->id, $updateData);

        $this->assertInstanceOf(ProductStockHistory::class, $result);
    }

    public function test_can_delete_productStockHistory(): void
    {
        $productStockHistory = ProductStockHistory::factory()->create();

        $result = $this->service->delete($productStockHistory->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('productStockHistorys', ['id' => $productStockHistory->id]);
    }

    public function test_can_find_productStockHistory_by_id(): void
    {
        $productStockHistory = ProductStockHistory::factory()->create();

        $result = $this->service->findById($productStockHistory->id);

        $this->assertInstanceOf(ProductStockHistory::class, $result);
        $this->assertEquals($productStockHistory->id, $result->id);
    }
}
