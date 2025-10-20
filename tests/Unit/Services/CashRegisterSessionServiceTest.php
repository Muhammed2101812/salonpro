<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\CashRegisterSession;
use App\Repositories\Contracts\CashRegisterSessionRepositoryInterface;
use App\Services\CashRegisterSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashRegisterSessionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CashRegisterSessionService $service;
    protected CashRegisterSessionRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CashRegisterSessionRepositoryInterface::class);
        $this->service = new CashRegisterSessionService($this->repository);
    }

    public function test_can_get_all_cashRegisterSessions(): void
    {
        CashRegisterSession::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_cashRegisterSessions(): void
    {
        CashRegisterSession::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_cashRegisterSession(): void
    {
        $data = CashRegisterSession::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(CashRegisterSession::class, $result);
        $this->assertDatabaseHas('cashRegisterSessions', ['id' => $result->id]);
    }

    public function test_can_update_cashRegisterSession(): void
    {
        $cashRegisterSession = CashRegisterSession::factory()->create();
        $updateData = CashRegisterSession::factory()->make()->toArray();

        $result = $this->service->update($cashRegisterSession->id, $updateData);

        $this->assertInstanceOf(CashRegisterSession::class, $result);
    }

    public function test_can_delete_cashRegisterSession(): void
    {
        $cashRegisterSession = CashRegisterSession::factory()->create();

        $result = $this->service->delete($cashRegisterSession->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('cashRegisterSessions', ['id' => $cashRegisterSession->id]);
    }

    public function test_can_find_cashRegisterSession_by_id(): void
    {
        $cashRegisterSession = CashRegisterSession::factory()->create();

        $result = $this->service->findById($cashRegisterSession->id);

        $this->assertInstanceOf(CashRegisterSession::class, $result);
        $this->assertEquals($cashRegisterSession->id, $result->id);
    }
}
