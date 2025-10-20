<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\LoyaltyPointTransaction;
use App\Repositories\Contracts\LoyaltyPointTransactionRepositoryInterface;
use App\Services\LoyaltyPointTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyPointTransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LoyaltyPointTransactionService $service;
    protected LoyaltyPointTransactionRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(LoyaltyPointTransactionRepositoryInterface::class);
        $this->service = new LoyaltyPointTransactionService($this->repository);
    }

    public function test_can_get_all_loyaltyPointTransactions(): void
    {
        LoyaltyPointTransaction::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_loyaltyPointTransactions(): void
    {
        LoyaltyPointTransaction::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_loyaltyPointTransaction(): void
    {
        $data = LoyaltyPointTransaction::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(LoyaltyPointTransaction::class, $result);
        $this->assertDatabaseHas('loyaltyPointTransactions', ['id' => $result->id]);
    }

    public function test_can_update_loyaltyPointTransaction(): void
    {
        $loyaltyPointTransaction = LoyaltyPointTransaction::factory()->create();
        $updateData = LoyaltyPointTransaction::factory()->make()->toArray();

        $result = $this->service->update($loyaltyPointTransaction->id, $updateData);

        $this->assertInstanceOf(LoyaltyPointTransaction::class, $result);
    }

    public function test_can_delete_loyaltyPointTransaction(): void
    {
        $loyaltyPointTransaction = LoyaltyPointTransaction::factory()->create();

        $result = $this->service->delete($loyaltyPointTransaction->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('loyaltyPointTransactions', ['id' => $loyaltyPointTransaction->id]);
    }

    public function test_can_find_loyaltyPointTransaction_by_id(): void
    {
        $loyaltyPointTransaction = LoyaltyPointTransaction::factory()->create();

        $result = $this->service->findById($loyaltyPointTransaction->id);

        $this->assertInstanceOf(LoyaltyPointTransaction::class, $result);
        $this->assertEquals($loyaltyPointTransaction->id, $result->id);
    }
}
