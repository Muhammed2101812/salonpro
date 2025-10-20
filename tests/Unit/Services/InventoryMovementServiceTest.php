<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\InventoryMovement;
use App\Repositories\Contracts\InventoryMovementRepositoryInterface;
use App\Services\InventoryMovementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryMovementServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InventoryMovementService $service;
    protected InventoryMovementRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(InventoryMovementRepositoryInterface::class);
        $this->service = new InventoryMovementService($this->repository);
    }

    public function test_can_get_all_inventoryMovements(): void
    {
        InventoryMovement::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_inventoryMovements(): void
    {
        InventoryMovement::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_inventoryMovement(): void
    {
        $data = InventoryMovement::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(InventoryMovement::class, $result);
        $this->assertDatabaseHas('inventoryMovements', ['id' => $result->id]);
    }

    public function test_can_update_inventoryMovement(): void
    {
        $inventoryMovement = InventoryMovement::factory()->create();
        $updateData = InventoryMovement::factory()->make()->toArray();

        $result = $this->service->update($inventoryMovement->id, $updateData);

        $this->assertInstanceOf(InventoryMovement::class, $result);
    }

    public function test_can_delete_inventoryMovement(): void
    {
        $inventoryMovement = InventoryMovement::factory()->create();

        $result = $this->service->delete($inventoryMovement->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('inventoryMovements', ['id' => $inventoryMovement->id]);
    }

    public function test_can_find_inventoryMovement_by_id(): void
    {
        $inventoryMovement = InventoryMovement::factory()->create();

        $result = $this->service->findById($inventoryMovement->id);

        $this->assertInstanceOf(InventoryMovement::class, $result);
        $this->assertEquals($inventoryMovement->id, $result->id);
    }
}
