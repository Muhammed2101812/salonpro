<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ProductBundleItem;
use App\Repositories\Contracts\ProductBundleItemRepositoryInterface;
use App\Services\ProductBundleItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductBundleItemServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductBundleItemService $service;
    protected ProductBundleItemRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ProductBundleItemRepositoryInterface::class);
        $this->service = new ProductBundleItemService($this->repository);
    }

    public function test_can_get_all_productBundleItems(): void
    {
        ProductBundleItem::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_productBundleItems(): void
    {
        ProductBundleItem::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_productBundleItem(): void
    {
        $data = ProductBundleItem::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ProductBundleItem::class, $result);
        $this->assertDatabaseHas('productBundleItems', ['id' => $result->id]);
    }

    public function test_can_update_productBundleItem(): void
    {
        $productBundleItem = ProductBundleItem::factory()->create();
        $updateData = ProductBundleItem::factory()->make()->toArray();

        $result = $this->service->update($productBundleItem->id, $updateData);

        $this->assertInstanceOf(ProductBundleItem::class, $result);
    }

    public function test_can_delete_productBundleItem(): void
    {
        $productBundleItem = ProductBundleItem::factory()->create();

        $result = $this->service->delete($productBundleItem->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('productBundleItems', ['id' => $productBundleItem->id]);
    }

    public function test_can_find_productBundleItem_by_id(): void
    {
        $productBundleItem = ProductBundleItem::factory()->create();

        $result = $this->service->findById($productBundleItem->id);

        $this->assertInstanceOf(ProductBundleItem::class, $result);
        $this->assertEquals($productBundleItem->id, $result->id);
    }
}
