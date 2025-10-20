<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ProductImage;
use App\Repositories\Contracts\ProductImageRepositoryInterface;
use App\Services\ProductImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductImageServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductImageService $service;
    protected ProductImageRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ProductImageRepositoryInterface::class);
        $this->service = new ProductImageService($this->repository);
    }

    public function test_can_get_all_productImages(): void
    {
        ProductImage::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_productImages(): void
    {
        ProductImage::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_productImage(): void
    {
        $data = ProductImage::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ProductImage::class, $result);
        $this->assertDatabaseHas('productImages', ['id' => $result->id]);
    }

    public function test_can_update_productImage(): void
    {
        $productImage = ProductImage::factory()->create();
        $updateData = ProductImage::factory()->make()->toArray();

        $result = $this->service->update($productImage->id, $updateData);

        $this->assertInstanceOf(ProductImage::class, $result);
    }

    public function test_can_delete_productImage(): void
    {
        $productImage = ProductImage::factory()->create();

        $result = $this->service->delete($productImage->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('productImages', ['id' => $productImage->id]);
    }

    public function test_can_find_productImage_by_id(): void
    {
        $productImage = ProductImage::factory()->create();

        $result = $this->service->findById($productImage->id);

        $this->assertInstanceOf(ProductImage::class, $result);
        $this->assertEquals($productImage->id, $result->id);
    }
}
