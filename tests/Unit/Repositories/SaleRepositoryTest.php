<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Sale;
use App\Repositories\Eloquent\SaleRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected SaleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new SaleRepository(new Sale());
    }

    public function test_can_get_all_records(): void
    {
        Sale::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = Sale::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(Sale::class, $result);
        $this->assertDatabaseHas('sales', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $sale = Sale::factory()->create();

        $result = $this->repository->find($sale->id);

        $this->assertInstanceOf(Sale::class, $result);
        $this->assertEquals($sale->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $sale = Sale::factory()->create();
        $updateData = Sale::factory()->make()->toArray();

        $result = $this->repository->update($sale->id, $updateData);

        $this->assertInstanceOf(Sale::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $sale = Sale::factory()->create();

        $result = $this->repository->delete($sale->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('sales', ['id' => $sale->id]);
    }
}
