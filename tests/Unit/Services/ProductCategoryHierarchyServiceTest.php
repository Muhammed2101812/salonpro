<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ProductCategoryHierarchy;
use App\Repositories\Contracts\ProductCategoryHierarchyRepositoryInterface;
use App\Services\ProductCategoryHierarchyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCategoryHierarchyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductCategoryHierarchyService $service;
    protected ProductCategoryHierarchyRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ProductCategoryHierarchyRepositoryInterface::class);
        $this->service = new ProductCategoryHierarchyService($this->repository);
    }

    public function test_can_get_all_productCategoryHierarchys(): void
    {
        ProductCategoryHierarchy::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_productCategoryHierarchys(): void
    {
        ProductCategoryHierarchy::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_productCategoryHierarchy(): void
    {
        $data = ProductCategoryHierarchy::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ProductCategoryHierarchy::class, $result);
        $this->assertDatabaseHas('productCategoryHierarchys', ['id' => $result->id]);
    }

    public function test_can_update_productCategoryHierarchy(): void
    {
        $productCategoryHierarchy = ProductCategoryHierarchy::factory()->create();
        $updateData = ProductCategoryHierarchy::factory()->make()->toArray();

        $result = $this->service->update($productCategoryHierarchy->id, $updateData);

        $this->assertInstanceOf(ProductCategoryHierarchy::class, $result);
    }

    public function test_can_delete_productCategoryHierarchy(): void
    {
        $productCategoryHierarchy = ProductCategoryHierarchy::factory()->create();

        $result = $this->service->delete($productCategoryHierarchy->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('productCategoryHierarchys', ['id' => $productCategoryHierarchy->id]);
    }

    public function test_can_find_productCategoryHierarchy_by_id(): void
    {
        $productCategoryHierarchy = ProductCategoryHierarchy::factory()->create();

        $result = $this->service->findById($productCategoryHierarchy->id);

        $this->assertInstanceOf(ProductCategoryHierarchy::class, $result);
        $this->assertEquals($productCategoryHierarchy->id, $result->id);
    }
}
