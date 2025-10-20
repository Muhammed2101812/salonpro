<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\StockTransfer;
use App\Repositories\Contracts\StockTransferRepositoryInterface;
use App\Services\StockTransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockTransferServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StockTransferService $service;
    protected StockTransferRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(StockTransferRepositoryInterface::class);
        $this->service = new StockTransferService($this->repository);
    }

    public function test_can_get_all_stockTransfers(): void
    {
        StockTransfer::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_stockTransfers(): void
    {
        StockTransfer::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_stockTransfer(): void
    {
        $data = StockTransfer::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(StockTransfer::class, $result);
        $this->assertDatabaseHas('stockTransfers', ['id' => $result->id]);
    }

    public function test_can_update_stockTransfer(): void
    {
        $stockTransfer = StockTransfer::factory()->create();
        $updateData = StockTransfer::factory()->make()->toArray();

        $result = $this->service->update($stockTransfer->id, $updateData);

        $this->assertInstanceOf(StockTransfer::class, $result);
    }

    public function test_can_delete_stockTransfer(): void
    {
        $stockTransfer = StockTransfer::factory()->create();

        $result = $this->service->delete($stockTransfer->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('stockTransfers', ['id' => $stockTransfer->id]);
    }

    public function test_can_find_stockTransfer_by_id(): void
    {
        $stockTransfer = StockTransfer::factory()->create();

        $result = $this->service->findById($stockTransfer->id);

        $this->assertInstanceOf(StockTransfer::class, $result);
        $this->assertEquals($stockTransfer->id, $result->id);
    }
}
