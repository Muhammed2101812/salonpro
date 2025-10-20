<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ProductPriceHistory;
use App\Repositories\Contracts\ProductPriceHistoryRepositoryInterface;
use App\Services\ProductPriceHistoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPriceHistoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductPriceHistoryService $service;
    protected ProductPriceHistoryRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ProductPriceHistoryRepositoryInterface::class);
        $this->service = new ProductPriceHistoryService($this->repository);
    }

    public function test_can_get_all_productPriceHistorys(): void
    {
        ProductPriceHistory::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_productPriceHistorys(): void
    {
        ProductPriceHistory::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_productPriceHistory(): void
    {
        $data = ProductPriceHistory::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ProductPriceHistory::class, $result);
        $this->assertDatabaseHas('productPriceHistorys', ['id' => $result->id]);
    }

    public function test_can_update_productPriceHistory(): void
    {
        $productPriceHistory = ProductPriceHistory::factory()->create();
        $updateData = ProductPriceHistory::factory()->make()->toArray();

        $result = $this->service->update($productPriceHistory->id, $updateData);

        $this->assertInstanceOf(ProductPriceHistory::class, $result);
    }

    public function test_can_delete_productPriceHistory(): void
    {
        $productPriceHistory = ProductPriceHistory::factory()->create();

        $result = $this->service->delete($productPriceHistory->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('productPriceHistorys', ['id' => $productPriceHistory->id]);
    }

    public function test_can_find_productPriceHistory_by_id(): void
    {
        $productPriceHistory = ProductPriceHistory::factory()->create();

        $result = $this->service->findById($productPriceHistory->id);

        $this->assertInstanceOf(ProductPriceHistory::class, $result);
        $this->assertEquals($productPriceHistory->id, $result->id);
    }
}
