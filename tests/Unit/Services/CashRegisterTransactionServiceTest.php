<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\CashRegisterTransaction;
use App\Repositories\Contracts\CashRegisterTransactionRepositoryInterface;
use App\Services\CashRegisterTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashRegisterTransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CashRegisterTransactionService $service;
    protected CashRegisterTransactionRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CashRegisterTransactionRepositoryInterface::class);
        $this->service = new CashRegisterTransactionService($this->repository);
    }

    public function test_can_get_all_cashRegisterTransactions(): void
    {
        CashRegisterTransaction::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_cashRegisterTransactions(): void
    {
        CashRegisterTransaction::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_cashRegisterTransaction(): void
    {
        $data = CashRegisterTransaction::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(CashRegisterTransaction::class, $result);
        $this->assertDatabaseHas('cashRegisterTransactions', ['id' => $result->id]);
    }

    public function test_can_update_cashRegisterTransaction(): void
    {
        $cashRegisterTransaction = CashRegisterTransaction::factory()->create();
        $updateData = CashRegisterTransaction::factory()->make()->toArray();

        $result = $this->service->update($cashRegisterTransaction->id, $updateData);

        $this->assertInstanceOf(CashRegisterTransaction::class, $result);
    }

    public function test_can_delete_cashRegisterTransaction(): void
    {
        $cashRegisterTransaction = CashRegisterTransaction::factory()->create();

        $result = $this->service->delete($cashRegisterTransaction->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('cashRegisterTransactions', ['id' => $cashRegisterTransaction->id]);
    }

    public function test_can_find_cashRegisterTransaction_by_id(): void
    {
        $cashRegisterTransaction = CashRegisterTransaction::factory()->create();

        $result = $this->service->findById($cashRegisterTransaction->id);

        $this->assertInstanceOf(CashRegisterTransaction::class, $result);
        $this->assertEquals($cashRegisterTransaction->id, $result->id);
    }
}
