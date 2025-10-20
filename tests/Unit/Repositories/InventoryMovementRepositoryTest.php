<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\InventoryMovement;
use App\Repositories\Eloquent\InventoryMovementRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryMovementRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected InventoryMovementRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new InventoryMovementRepository(new InventoryMovement());
    }

    public function test_can_get_all_records(): void
    {
        InventoryMovement::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = InventoryMovement::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(InventoryMovement::class, $result);
        $this->assertDatabaseHas('inventoryMovements', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $inventoryMovement = InventoryMovement::factory()->create();

        $result = $this->repository->find($inventoryMovement->id);

        $this->assertInstanceOf(InventoryMovement::class, $result);
        $this->assertEquals($inventoryMovement->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $inventoryMovement = InventoryMovement::factory()->create();
        $updateData = InventoryMovement::factory()->make()->toArray();

        $result = $this->repository->update($inventoryMovement->id, $updateData);

        $this->assertInstanceOf(InventoryMovement::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $inventoryMovement = InventoryMovement::factory()->create();

        $result = $this->repository->delete($inventoryMovement->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('inventoryMovements', ['id' => $inventoryMovement->id]);
    }
}
