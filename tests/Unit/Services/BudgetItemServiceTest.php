<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\BudgetItem;
use App\Repositories\Contracts\BudgetItemRepositoryInterface;
use App\Services\BudgetItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetItemServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BudgetItemService $service;
    protected BudgetItemRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(BudgetItemRepositoryInterface::class);
        $this->service = new BudgetItemService($this->repository);
    }

    public function test_can_get_all_budgetItems(): void
    {
        BudgetItem::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_budgetItems(): void
    {
        BudgetItem::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_budgetItem(): void
    {
        $data = BudgetItem::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(BudgetItem::class, $result);
        $this->assertDatabaseHas('budgetItems', ['id' => $result->id]);
    }

    public function test_can_update_budgetItem(): void
    {
        $budgetItem = BudgetItem::factory()->create();
        $updateData = BudgetItem::factory()->make()->toArray();

        $result = $this->service->update($budgetItem->id, $updateData);

        $this->assertInstanceOf(BudgetItem::class, $result);
    }

    public function test_can_delete_budgetItem(): void
    {
        $budgetItem = BudgetItem::factory()->create();

        $result = $this->service->delete($budgetItem->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('budgetItems', ['id' => $budgetItem->id]);
    }

    public function test_can_find_budgetItem_by_id(): void
    {
        $budgetItem = BudgetItem::factory()->create();

        $result = $this->service->findById($budgetItem->id);

        $this->assertInstanceOf(BudgetItem::class, $result);
        $this->assertEquals($budgetItem->id, $result->id);
    }
}
