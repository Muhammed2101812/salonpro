<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\StockAuditItem;
use App\Repositories\Contracts\StockAuditItemRepositoryInterface;
use App\Services\StockAuditItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAuditItemServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StockAuditItemService $service;
    protected StockAuditItemRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(StockAuditItemRepositoryInterface::class);
        $this->service = new StockAuditItemService($this->repository);
    }

    public function test_can_get_all_stockAuditItems(): void
    {
        StockAuditItem::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_stockAuditItems(): void
    {
        StockAuditItem::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_stockAuditItem(): void
    {
        $data = StockAuditItem::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(StockAuditItem::class, $result);
        $this->assertDatabaseHas('stockAuditItems', ['id' => $result->id]);
    }

    public function test_can_update_stockAuditItem(): void
    {
        $stockAuditItem = StockAuditItem::factory()->create();
        $updateData = StockAuditItem::factory()->make()->toArray();

        $result = $this->service->update($stockAuditItem->id, $updateData);

        $this->assertInstanceOf(StockAuditItem::class, $result);
    }

    public function test_can_delete_stockAuditItem(): void
    {
        $stockAuditItem = StockAuditItem::factory()->create();

        $result = $this->service->delete($stockAuditItem->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('stockAuditItems', ['id' => $stockAuditItem->id]);
    }

    public function test_can_find_stockAuditItem_by_id(): void
    {
        $stockAuditItem = StockAuditItem::factory()->create();

        $result = $this->service->findById($stockAuditItem->id);

        $this->assertInstanceOf(StockAuditItem::class, $result);
        $this->assertEquals($stockAuditItem->id, $result->id);
    }
}
