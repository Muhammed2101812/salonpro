<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ProductBundle;
use App\Repositories\Contracts\ProductBundleRepositoryInterface;
use App\Services\ProductBundleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductBundleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductBundleService $service;
    protected ProductBundleRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ProductBundleRepositoryInterface::class);
        $this->service = new ProductBundleService($this->repository);
    }

    public function test_can_get_all_productBundles(): void
    {
        ProductBundle::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_productBundles(): void
    {
        ProductBundle::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_productBundle(): void
    {
        $data = ProductBundle::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ProductBundle::class, $result);
        $this->assertDatabaseHas('productBundles', ['id' => $result->id]);
    }

    public function test_can_update_productBundle(): void
    {
        $productBundle = ProductBundle::factory()->create();
        $updateData = ProductBundle::factory()->make()->toArray();

        $result = $this->service->update($productBundle->id, $updateData);

        $this->assertInstanceOf(ProductBundle::class, $result);
    }

    public function test_can_delete_productBundle(): void
    {
        $productBundle = ProductBundle::factory()->create();

        $result = $this->service->delete($productBundle->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('productBundles', ['id' => $productBundle->id]);
    }

    public function test_can_find_productBundle_by_id(): void
    {
        $productBundle = ProductBundle::factory()->create();

        $result = $this->service->findById($productBundle->id);

        $this->assertInstanceOf(ProductBundle::class, $result);
        $this->assertEquals($productBundle->id, $result->id);
    }
}
