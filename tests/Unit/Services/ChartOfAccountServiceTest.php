<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ChartOfAccount;
use App\Repositories\Contracts\ChartOfAccountRepositoryInterface;
use App\Services\ChartOfAccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChartOfAccountServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ChartOfAccountService $service;
    protected ChartOfAccountRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ChartOfAccountRepositoryInterface::class);
        $this->service = new ChartOfAccountService($this->repository);
    }

    public function test_can_get_all_chartOfAccounts(): void
    {
        ChartOfAccount::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_chartOfAccounts(): void
    {
        ChartOfAccount::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_chartOfAccount(): void
    {
        $data = ChartOfAccount::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ChartOfAccount::class, $result);
        $this->assertDatabaseHas('chartOfAccounts', ['id' => $result->id]);
    }

    public function test_can_update_chartOfAccount(): void
    {
        $chartOfAccount = ChartOfAccount::factory()->create();
        $updateData = ChartOfAccount::factory()->make()->toArray();

        $result = $this->service->update($chartOfAccount->id, $updateData);

        $this->assertInstanceOf(ChartOfAccount::class, $result);
    }

    public function test_can_delete_chartOfAccount(): void
    {
        $chartOfAccount = ChartOfAccount::factory()->create();

        $result = $this->service->delete($chartOfAccount->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('chartOfAccounts', ['id' => $chartOfAccount->id]);
    }

    public function test_can_find_chartOfAccount_by_id(): void
    {
        $chartOfAccount = ChartOfAccount::factory()->create();

        $result = $this->service->findById($chartOfAccount->id);

        $this->assertInstanceOf(ChartOfAccount::class, $result);
        $this->assertEquals($chartOfAccount->id, $result->id);
    }
}
