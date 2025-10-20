<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\StockTransfer;
use App\Repositories\Eloquent\StockTransferRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockTransferRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected StockTransferRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new StockTransferRepository(new StockTransfer());
    }

    public function test_can_get_all_records(): void
    {
        StockTransfer::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = StockTransfer::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(StockTransfer::class, $result);
        $this->assertDatabaseHas('stockTransfers', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $stockTransfer = StockTransfer::factory()->create();

        $result = $this->repository->find($stockTransfer->id);

        $this->assertInstanceOf(StockTransfer::class, $result);
        $this->assertEquals($stockTransfer->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $stockTransfer = StockTransfer::factory()->create();
        $updateData = StockTransfer::factory()->make()->toArray();

        $result = $this->repository->update($stockTransfer->id, $updateData);

        $this->assertInstanceOf(StockTransfer::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $stockTransfer = StockTransfer::factory()->create();

        $result = $this->repository->delete($stockTransfer->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('stockTransfers', ['id' => $stockTransfer->id]);
    }
}
