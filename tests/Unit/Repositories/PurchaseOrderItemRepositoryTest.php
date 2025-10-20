<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\PurchaseOrderItem;
use App\Repositories\Eloquent\PurchaseOrderItemRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderItemRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected PurchaseOrderItemRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PurchaseOrderItemRepository(new PurchaseOrderItem());
    }

    public function test_can_get_all_records(): void
    {
        PurchaseOrderItem::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = PurchaseOrderItem::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(PurchaseOrderItem::class, $result);
        $this->assertDatabaseHas('purchaseOrderItems', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $purchaseOrderItem = PurchaseOrderItem::factory()->create();

        $result = $this->repository->find($purchaseOrderItem->id);

        $this->assertInstanceOf(PurchaseOrderItem::class, $result);
        $this->assertEquals($purchaseOrderItem->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $purchaseOrderItem = PurchaseOrderItem::factory()->create();
        $updateData = PurchaseOrderItem::factory()->make()->toArray();

        $result = $this->repository->update($purchaseOrderItem->id, $updateData);

        $this->assertInstanceOf(PurchaseOrderItem::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $purchaseOrderItem = PurchaseOrderItem::factory()->create();

        $result = $this->repository->delete($purchaseOrderItem->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('purchaseOrderItems', ['id' => $purchaseOrderItem->id]);
    }
}
