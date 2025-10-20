<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ProductVariant;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use App\Services\ProductVariantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductVariantServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductVariantService $service;
    protected ProductVariantRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ProductVariantRepositoryInterface::class);
        $this->service = new ProductVariantService($this->repository);
    }

    public function test_can_get_all_productVariants(): void
    {
        ProductVariant::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_productVariants(): void
    {
        ProductVariant::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_productVariant(): void
    {
        $data = ProductVariant::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ProductVariant::class, $result);
        $this->assertDatabaseHas('productVariants', ['id' => $result->id]);
    }

    public function test_can_update_productVariant(): void
    {
        $productVariant = ProductVariant::factory()->create();
        $updateData = ProductVariant::factory()->make()->toArray();

        $result = $this->service->update($productVariant->id, $updateData);

        $this->assertInstanceOf(ProductVariant::class, $result);
    }

    public function test_can_delete_productVariant(): void
    {
        $productVariant = ProductVariant::factory()->create();

        $result = $this->service->delete($productVariant->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('productVariants', ['id' => $productVariant->id]);
    }

    public function test_can_find_productVariant_by_id(): void
    {
        $productVariant = ProductVariant::factory()->create();

        $result = $this->service->findById($productVariant->id);

        $this->assertInstanceOf(ProductVariant::class, $result);
        $this->assertEquals($productVariant->id, $result->id);
    }
}
