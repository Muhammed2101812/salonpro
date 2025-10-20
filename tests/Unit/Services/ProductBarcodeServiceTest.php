<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ProductBarcode;
use App\Repositories\Contracts\ProductBarcodeRepositoryInterface;
use App\Services\ProductBarcodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductBarcodeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductBarcodeService $service;
    protected ProductBarcodeRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ProductBarcodeRepositoryInterface::class);
        $this->service = new ProductBarcodeService($this->repository);
    }

    public function test_can_get_all_productBarcodes(): void
    {
        ProductBarcode::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_productBarcodes(): void
    {
        ProductBarcode::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_productBarcode(): void
    {
        $data = ProductBarcode::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ProductBarcode::class, $result);
        $this->assertDatabaseHas('productBarcodes', ['id' => $result->id]);
    }

    public function test_can_update_productBarcode(): void
    {
        $productBarcode = ProductBarcode::factory()->create();
        $updateData = ProductBarcode::factory()->make()->toArray();

        $result = $this->service->update($productBarcode->id, $updateData);

        $this->assertInstanceOf(ProductBarcode::class, $result);
    }

    public function test_can_delete_productBarcode(): void
    {
        $productBarcode = ProductBarcode::factory()->create();

        $result = $this->service->delete($productBarcode->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('productBarcodes', ['id' => $productBarcode->id]);
    }

    public function test_can_find_productBarcode_by_id(): void
    {
        $productBarcode = ProductBarcode::factory()->create();

        $result = $this->service->findById($productBarcode->id);

        $this->assertInstanceOf(ProductBarcode::class, $result);
        $this->assertEquals($productBarcode->id, $result->id);
    }
}
