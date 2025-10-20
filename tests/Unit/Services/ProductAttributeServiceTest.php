<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ProductAttribute;
use App\Repositories\Contracts\ProductAttributeRepositoryInterface;
use App\Services\ProductAttributeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAttributeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductAttributeService $service;
    protected ProductAttributeRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ProductAttributeRepositoryInterface::class);
        $this->service = new ProductAttributeService($this->repository);
    }

    public function test_can_get_all_productAttributes(): void
    {
        ProductAttribute::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_productAttributes(): void
    {
        ProductAttribute::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_productAttribute(): void
    {
        $data = ProductAttribute::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ProductAttribute::class, $result);
        $this->assertDatabaseHas('productAttributes', ['id' => $result->id]);
    }

    public function test_can_update_productAttribute(): void
    {
        $productAttribute = ProductAttribute::factory()->create();
        $updateData = ProductAttribute::factory()->make()->toArray();

        $result = $this->service->update($productAttribute->id, $updateData);

        $this->assertInstanceOf(ProductAttribute::class, $result);
    }

    public function test_can_delete_productAttribute(): void
    {
        $productAttribute = ProductAttribute::factory()->create();

        $result = $this->service->delete($productAttribute->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('productAttributes', ['id' => $productAttribute->id]);
    }

    public function test_can_find_productAttribute_by_id(): void
    {
        $productAttribute = ProductAttribute::factory()->create();

        $result = $this->service->findById($productAttribute->id);

        $this->assertInstanceOf(ProductAttribute::class, $result);
        $this->assertEquals($productAttribute->id, $result->id);
    }
}
