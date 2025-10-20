<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\BankAccount;
use App\Repositories\Contracts\BankAccountRepositoryInterface;
use App\Services\BankAccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankAccountServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BankAccountService $service;
    protected BankAccountRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(BankAccountRepositoryInterface::class);
        $this->service = new BankAccountService($this->repository);
    }

    public function test_can_get_all_bankAccounts(): void
    {
        BankAccount::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_bankAccounts(): void
    {
        BankAccount::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_bankAccount(): void
    {
        $data = BankAccount::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(BankAccount::class, $result);
        $this->assertDatabaseHas('bankAccounts', ['id' => $result->id]);
    }

    public function test_can_update_bankAccount(): void
    {
        $bankAccount = BankAccount::factory()->create();
        $updateData = BankAccount::factory()->make()->toArray();

        $result = $this->service->update($bankAccount->id, $updateData);

        $this->assertInstanceOf(BankAccount::class, $result);
    }

    public function test_can_delete_bankAccount(): void
    {
        $bankAccount = BankAccount::factory()->create();

        $result = $this->service->delete($bankAccount->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('bankAccounts', ['id' => $bankAccount->id]);
    }

    public function test_can_find_bankAccount_by_id(): void
    {
        $bankAccount = BankAccount::factory()->create();

        $result = $this->service->findById($bankAccount->id);

        $this->assertInstanceOf(BankAccount::class, $result);
        $this->assertEquals($bankAccount->id, $result->id);
    }
}
