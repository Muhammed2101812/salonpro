<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\StockAlert;
use App\Repositories\Eloquent\StockAlertRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAlertRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected StockAlertRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new StockAlertRepository(new StockAlert());
    }

    public function test_can_get_all_records(): void
    {
        StockAlert::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = StockAlert::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(StockAlert::class, $result);
        $this->assertDatabaseHas('stockAlerts', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $stockAlert = StockAlert::factory()->create();

        $result = $this->repository->find($stockAlert->id);

        $this->assertInstanceOf(StockAlert::class, $result);
        $this->assertEquals($stockAlert->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $stockAlert = StockAlert::factory()->create();
        $updateData = StockAlert::factory()->make()->toArray();

        $result = $this->repository->update($stockAlert->id, $updateData);

        $this->assertInstanceOf(StockAlert::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $stockAlert = StockAlert::factory()->create();

        $result = $this->repository->delete($stockAlert->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('stockAlerts', ['id' => $stockAlert->id]);
    }
}
