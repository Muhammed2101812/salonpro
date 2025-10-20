<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\StockAudit;
use App\Repositories\Eloquent\StockAuditRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAuditRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected StockAuditRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new StockAuditRepository(new StockAudit());
    }

    public function test_can_get_all_records(): void
    {
        StockAudit::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = StockAudit::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(StockAudit::class, $result);
        $this->assertDatabaseHas('stockAudits', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $stockAudit = StockAudit::factory()->create();

        $result = $this->repository->find($stockAudit->id);

        $this->assertInstanceOf(StockAudit::class, $result);
        $this->assertEquals($stockAudit->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $stockAudit = StockAudit::factory()->create();
        $updateData = StockAudit::factory()->make()->toArray();

        $result = $this->repository->update($stockAudit->id, $updateData);

        $this->assertInstanceOf(StockAudit::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $stockAudit = StockAudit::factory()->create();

        $result = $this->repository->delete($stockAudit->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('stockAudits', ['id' => $stockAudit->id]);
    }
}
