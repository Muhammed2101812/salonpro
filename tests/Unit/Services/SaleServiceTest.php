<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Sale;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SaleService $service;
    protected SaleRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(SaleRepositoryInterface::class);
        $this->service = new SaleService($this->repository);
    }

    public function test_can_get_all_sales(): void
    {
        Sale::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_sales(): void
    {
        Sale::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_sale(): void
    {
        $data = Sale::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Sale::class, $result);
        $this->assertDatabaseHas('sales', ['id' => $result->id]);
    }

    public function test_can_update_sale(): void
    {
        $sale = Sale::factory()->create();
        $updateData = Sale::factory()->make()->toArray();

        $result = $this->service->update($sale->id, $updateData);

        $this->assertInstanceOf(Sale::class, $result);
    }

    public function test_can_delete_sale(): void
    {
        $sale = Sale::factory()->create();

        $result = $this->service->delete($sale->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('sales', ['id' => $sale->id]);
    }

    public function test_can_find_sale_by_id(): void
    {
        $sale = Sale::factory()->create();

        $result = $this->service->findById($sale->id);

        $this->assertInstanceOf(Sale::class, $result);
        $this->assertEquals($sale->id, $result->id);
    }
}
