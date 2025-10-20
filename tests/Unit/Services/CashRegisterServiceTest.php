<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\CashRegister;
use App\Repositories\Contracts\CashRegisterRepositoryInterface;
use App\Services\CashRegisterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashRegisterServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CashRegisterService $service;
    protected CashRegisterRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CashRegisterRepositoryInterface::class);
        $this->service = new CashRegisterService($this->repository);
    }

    public function test_can_get_all_cashRegisters(): void
    {
        CashRegister::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_cashRegisters(): void
    {
        CashRegister::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_cashRegister(): void
    {
        $data = CashRegister::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(CashRegister::class, $result);
        $this->assertDatabaseHas('cashRegisters', ['id' => $result->id]);
    }

    public function test_can_update_cashRegister(): void
    {
        $cashRegister = CashRegister::factory()->create();
        $updateData = CashRegister::factory()->make()->toArray();

        $result = $this->service->update($cashRegister->id, $updateData);

        $this->assertInstanceOf(CashRegister::class, $result);
    }

    public function test_can_delete_cashRegister(): void
    {
        $cashRegister = CashRegister::factory()->create();

        $result = $this->service->delete($cashRegister->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('cashRegisters', ['id' => $cashRegister->id]);
    }

    public function test_can_find_cashRegister_by_id(): void
    {
        $cashRegister = CashRegister::factory()->create();

        $result = $this->service->findById($cashRegister->id);

        $this->assertInstanceOf(CashRegister::class, $result);
        $this->assertEquals($cashRegister->id, $result->id);
    }
}
