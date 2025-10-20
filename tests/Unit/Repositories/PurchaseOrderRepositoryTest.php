<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\PurchaseOrder;
use App\Repositories\Eloquent\PurchaseOrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected PurchaseOrderRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PurchaseOrderRepository(new PurchaseOrder());
    }

    public function test_can_get_all_records(): void
    {
        PurchaseOrder::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = PurchaseOrder::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(PurchaseOrder::class, $result);
        $this->assertDatabaseHas('purchaseOrders', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $purchaseOrder = PurchaseOrder::factory()->create();

        $result = $this->repository->find($purchaseOrder->id);

        $this->assertInstanceOf(PurchaseOrder::class, $result);
        $this->assertEquals($purchaseOrder->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $purchaseOrder = PurchaseOrder::factory()->create();
        $updateData = PurchaseOrder::factory()->make()->toArray();

        $result = $this->repository->update($purchaseOrder->id, $updateData);

        $this->assertInstanceOf(PurchaseOrder::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $purchaseOrder = PurchaseOrder::factory()->create();

        $result = $this->repository->delete($purchaseOrder->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('purchaseOrders', ['id' => $purchaseOrder->id]);
    }
}
