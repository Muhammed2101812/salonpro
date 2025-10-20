<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\PurchaseOrder;
use App\Repositories\Contracts\PurchaseOrderRepositoryInterface;
use App\Services\PurchaseOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PurchaseOrderService $service;
    protected PurchaseOrderRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(PurchaseOrderRepositoryInterface::class);
        $this->service = new PurchaseOrderService($this->repository);
    }

    public function test_can_get_all_purchaseOrders(): void
    {
        PurchaseOrder::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_purchaseOrders(): void
    {
        PurchaseOrder::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_purchaseOrder(): void
    {
        $data = PurchaseOrder::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(PurchaseOrder::class, $result);
        $this->assertDatabaseHas('purchaseOrders', ['id' => $result->id]);
    }

    public function test_can_update_purchaseOrder(): void
    {
        $purchaseOrder = PurchaseOrder::factory()->create();
        $updateData = PurchaseOrder::factory()->make()->toArray();

        $result = $this->service->update($purchaseOrder->id, $updateData);

        $this->assertInstanceOf(PurchaseOrder::class, $result);
    }

    public function test_can_delete_purchaseOrder(): void
    {
        $purchaseOrder = PurchaseOrder::factory()->create();

        $result = $this->service->delete($purchaseOrder->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('purchaseOrders', ['id' => $purchaseOrder->id]);
    }

    public function test_can_find_purchaseOrder_by_id(): void
    {
        $purchaseOrder = PurchaseOrder::factory()->create();

        $result = $this->service->findById($purchaseOrder->id);

        $this->assertInstanceOf(PurchaseOrder::class, $result);
        $this->assertEquals($purchaseOrder->id, $result->id);
    }
}
