<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\BankTransaction;
use App\Repositories\Contracts\BankTransactionRepositoryInterface;
use App\Services\BankTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankTransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BankTransactionService $service;
    protected BankTransactionRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(BankTransactionRepositoryInterface::class);
        $this->service = new BankTransactionService($this->repository);
    }

    public function test_can_get_all_bankTransactions(): void
    {
        BankTransaction::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_bankTransactions(): void
    {
        BankTransaction::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_bankTransaction(): void
    {
        $data = BankTransaction::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(BankTransaction::class, $result);
        $this->assertDatabaseHas('bankTransactions', ['id' => $result->id]);
    }

    public function test_can_update_bankTransaction(): void
    {
        $bankTransaction = BankTransaction::factory()->create();
        $updateData = BankTransaction::factory()->make()->toArray();

        $result = $this->service->update($bankTransaction->id, $updateData);

        $this->assertInstanceOf(BankTransaction::class, $result);
    }

    public function test_can_delete_bankTransaction(): void
    {
        $bankTransaction = BankTransaction::factory()->create();

        $result = $this->service->delete($bankTransaction->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('bankTransactions', ['id' => $bankTransaction->id]);
    }

    public function test_can_find_bankTransaction_by_id(): void
    {
        $bankTransaction = BankTransaction::factory()->create();

        $result = $this->service->findById($bankTransaction->id);

        $this->assertInstanceOf(BankTransaction::class, $result);
        $this->assertEquals($bankTransaction->id, $result->id);
    }
}
