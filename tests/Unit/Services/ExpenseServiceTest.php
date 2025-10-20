<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Expense;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Services\ExpenseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ExpenseService $service;
    protected ExpenseRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ExpenseRepositoryInterface::class);
        $this->service = new ExpenseService($this->repository);
    }

    public function test_can_get_all_expenses(): void
    {
        Expense::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_expenses(): void
    {
        Expense::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_expense(): void
    {
        $data = Expense::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Expense::class, $result);
        $this->assertDatabaseHas('expenses', ['id' => $result->id]);
    }

    public function test_can_update_expense(): void
    {
        $expense = Expense::factory()->create();
        $updateData = Expense::factory()->make()->toArray();

        $result = $this->service->update($expense->id, $updateData);

        $this->assertInstanceOf(Expense::class, $result);
    }

    public function test_can_delete_expense(): void
    {
        $expense = Expense::factory()->create();

        $result = $this->service->delete($expense->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('expenses', ['id' => $expense->id]);
    }

    public function test_can_find_expense_by_id(): void
    {
        $expense = Expense::factory()->create();

        $result = $this->service->findById($expense->id);

        $this->assertInstanceOf(Expense::class, $result);
        $this->assertEquals($expense->id, $result->id);
    }
}
