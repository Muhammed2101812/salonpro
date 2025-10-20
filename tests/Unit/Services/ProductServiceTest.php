<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $service;
    protected ProductRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ProductRepositoryInterface::class);
        $this->service = new ProductService($this->repository);
    }

    public function test_can_get_all_products(): void
    {
        Product::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_products(): void
    {
        Product::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_product(): void
    {
        $data = Product::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertDatabaseHas('products', ['id' => $result->id]);
    }

    public function test_can_update_product(): void
    {
        $product = Product::factory()->create();
        $updateData = Product::factory()->make()->toArray();

        $result = $this->service->update($product->id, $updateData);

        $this->assertInstanceOf(Product::class, $result);
    }

    public function test_can_delete_product(): void
    {
        $product = Product::factory()->create();

        $result = $this->service->delete($product->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_can_find_product_by_id(): void
    {
        $product = Product::factory()->create();

        $result = $this->service->findById($product->id);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($product->id, $result->id);
    }
}
