<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\StockAudit;
use App\Repositories\Contracts\StockAuditRepositoryInterface;
use App\Services\StockAuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAuditServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StockAuditService $service;
    protected StockAuditRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(StockAuditRepositoryInterface::class);
        $this->service = new StockAuditService($this->repository);
    }

    public function test_can_get_all_stockAudits(): void
    {
        StockAudit::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_stockAudits(): void
    {
        StockAudit::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_stockAudit(): void
    {
        $data = StockAudit::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(StockAudit::class, $result);
        $this->assertDatabaseHas('stockAudits', ['id' => $result->id]);
    }

    public function test_can_update_stockAudit(): void
    {
        $stockAudit = StockAudit::factory()->create();
        $updateData = StockAudit::factory()->make()->toArray();

        $result = $this->service->update($stockAudit->id, $updateData);

        $this->assertInstanceOf(StockAudit::class, $result);
    }

    public function test_can_delete_stockAudit(): void
    {
        $stockAudit = StockAudit::factory()->create();

        $result = $this->service->delete($stockAudit->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('stockAudits', ['id' => $stockAudit->id]);
    }

    public function test_can_find_stockAudit_by_id(): void
    {
        $stockAudit = StockAudit::factory()->create();

        $result = $this->service->findById($stockAudit->id);

        $this->assertInstanceOf(StockAudit::class, $result);
        $this->assertEquals($stockAudit->id, $result->id);
    }
}
