<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ProductDiscount;
use App\Repositories\Contracts\ProductDiscountRepositoryInterface;
use App\Services\ProductDiscountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDiscountServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductDiscountService $service;
    protected ProductDiscountRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ProductDiscountRepositoryInterface::class);
        $this->service = new ProductDiscountService($this->repository);
    }

    public function test_can_get_all_productDiscounts(): void
    {
        ProductDiscount::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_productDiscounts(): void
    {
        ProductDiscount::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_productDiscount(): void
    {
        $data = ProductDiscount::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ProductDiscount::class, $result);
        $this->assertDatabaseHas('productDiscounts', ['id' => $result->id]);
    }

    public function test_can_update_productDiscount(): void
    {
        $productDiscount = ProductDiscount::factory()->create();
        $updateData = ProductDiscount::factory()->make()->toArray();

        $result = $this->service->update($productDiscount->id, $updateData);

        $this->assertInstanceOf(ProductDiscount::class, $result);
    }

    public function test_can_delete_productDiscount(): void
    {
        $productDiscount = ProductDiscount::factory()->create();

        $result = $this->service->delete($productDiscount->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('productDiscounts', ['id' => $productDiscount->id]);
    }

    public function test_can_find_productDiscount_by_id(): void
    {
        $productDiscount = ProductDiscount::factory()->create();

        $result = $this->service->findById($productDiscount->id);

        $this->assertInstanceOf(ProductDiscount::class, $result);
        $this->assertEquals($productDiscount->id, $result->id);
    }
}
