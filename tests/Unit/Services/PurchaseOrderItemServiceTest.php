<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\PurchaseOrderItem;
use App\Repositories\Contracts\PurchaseOrderItemRepositoryInterface;
use App\Services\PurchaseOrderItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderItemServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PurchaseOrderItemService $service;
    protected PurchaseOrderItemRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(PurchaseOrderItemRepositoryInterface::class);
        $this->service = new PurchaseOrderItemService($this->repository);
    }

    public function test_can_get_all_purchaseOrderItems(): void
    {
        PurchaseOrderItem::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_purchaseOrderItems(): void
    {
        PurchaseOrderItem::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_purchaseOrderItem(): void
    {
        $data = PurchaseOrderItem::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(PurchaseOrderItem::class, $result);
        $this->assertDatabaseHas('purchaseOrderItems', ['id' => $result->id]);
    }

    public function test_can_update_purchaseOrderItem(): void
    {
        $purchaseOrderItem = PurchaseOrderItem::factory()->create();
        $updateData = PurchaseOrderItem::factory()->make()->toArray();

        $result = $this->service->update($purchaseOrderItem->id, $updateData);

        $this->assertInstanceOf(PurchaseOrderItem::class, $result);
    }

    public function test_can_delete_purchaseOrderItem(): void
    {
        $purchaseOrderItem = PurchaseOrderItem::factory()->create();

        $result = $this->service->delete($purchaseOrderItem->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('purchaseOrderItems', ['id' => $purchaseOrderItem->id]);
    }

    public function test_can_find_purchaseOrderItem_by_id(): void
    {
        $purchaseOrderItem = PurchaseOrderItem::factory()->create();

        $result = $this->service->findById($purchaseOrderItem->id);

        $this->assertInstanceOf(PurchaseOrderItem::class, $result);
        $this->assertEquals($purchaseOrderItem->id, $result->id);
    }
}
