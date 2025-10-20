<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ProductSupplierPrice;
use App\Repositories\Contracts\ProductSupplierPriceRepositoryInterface;
use App\Services\ProductSupplierPriceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSupplierPriceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductSupplierPriceService $service;
    protected ProductSupplierPriceRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ProductSupplierPriceRepositoryInterface::class);
        $this->service = new ProductSupplierPriceService($this->repository);
    }

    public function test_can_get_all_productSupplierPrices(): void
    {
        ProductSupplierPrice::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_productSupplierPrices(): void
    {
        ProductSupplierPrice::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_productSupplierPrice(): void
    {
        $data = ProductSupplierPrice::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ProductSupplierPrice::class, $result);
        $this->assertDatabaseHas('productSupplierPrices', ['id' => $result->id]);
    }

    public function test_can_update_productSupplierPrice(): void
    {
        $productSupplierPrice = ProductSupplierPrice::factory()->create();
        $updateData = ProductSupplierPrice::factory()->make()->toArray();

        $result = $this->service->update($productSupplierPrice->id, $updateData);

        $this->assertInstanceOf(ProductSupplierPrice::class, $result);
    }

    public function test_can_delete_productSupplierPrice(): void
    {
        $productSupplierPrice = ProductSupplierPrice::factory()->create();

        $result = $this->service->delete($productSupplierPrice->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('productSupplierPrices', ['id' => $productSupplierPrice->id]);
    }

    public function test_can_find_productSupplierPrice_by_id(): void
    {
        $productSupplierPrice = ProductSupplierPrice::factory()->create();

        $result = $this->service->findById($productSupplierPrice->id);

        $this->assertInstanceOf(ProductSupplierPrice::class, $result);
        $this->assertEquals($productSupplierPrice->id, $result->id);
    }
}
