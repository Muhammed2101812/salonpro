<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Expense;
use App\Repositories\Eloquent\ExpenseRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ExpenseRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ExpenseRepository(new Expense());
    }

    public function test_can_get_all_records(): void
    {
        Expense::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = Expense::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(Expense::class, $result);
        $this->assertDatabaseHas('expenses', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $expense = Expense::factory()->create();

        $result = $this->repository->find($expense->id);

        $this->assertInstanceOf(Expense::class, $result);
        $this->assertEquals($expense->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $expense = Expense::factory()->create();
        $updateData = Expense::factory()->make()->toArray();

        $result = $this->repository->update($expense->id, $updateData);

        $this->assertInstanceOf(Expense::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $expense = Expense::factory()->create();

        $result = $this->repository->delete($expense->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('expenses', ['id' => $expense->id]);
    }
}
