<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ProductAttributeValue;
use App\Repositories\Contracts\ProductAttributeValueRepositoryInterface;
use App\Services\ProductAttributeValueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAttributeValueServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductAttributeValueService $service;
    protected ProductAttributeValueRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ProductAttributeValueRepositoryInterface::class);
        $this->service = new ProductAttributeValueService($this->repository);
    }

    public function test_can_get_all_productAttributeValues(): void
    {
        ProductAttributeValue::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_productAttributeValues(): void
    {
        ProductAttributeValue::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_productAttributeValue(): void
    {
        $data = ProductAttributeValue::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ProductAttributeValue::class, $result);
        $this->assertDatabaseHas('productAttributeValues', ['id' => $result->id]);
    }

    public function test_can_update_productAttributeValue(): void
    {
        $productAttributeValue = ProductAttributeValue::factory()->create();
        $updateData = ProductAttributeValue::factory()->make()->toArray();

        $result = $this->service->update($productAttributeValue->id, $updateData);

        $this->assertInstanceOf(ProductAttributeValue::class, $result);
    }

    public function test_can_delete_productAttributeValue(): void
    {
        $productAttributeValue = ProductAttributeValue::factory()->create();

        $result = $this->service->delete($productAttributeValue->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('productAttributeValues', ['id' => $productAttributeValue->id]);
    }

    public function test_can_find_productAttributeValue_by_id(): void
    {
        $productAttributeValue = ProductAttributeValue::factory()->create();

        $result = $this->service->findById($productAttributeValue->id);

        $this->assertInstanceOf(ProductAttributeValue::class, $result);
        $this->assertEquals($productAttributeValue->id, $result->id);
    }
}
