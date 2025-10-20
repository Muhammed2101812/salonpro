<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Supplier;
use App\Repositories\Eloquent\SupplierRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected SupplierRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new SupplierRepository(new Supplier());
    }

    public function test_can_get_all_records(): void
    {
        Supplier::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = Supplier::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(Supplier::class, $result);
        $this->assertDatabaseHas('suppliers', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $supplier = Supplier::factory()->create();

        $result = $this->repository->find($supplier->id);

        $this->assertInstanceOf(Supplier::class, $result);
        $this->assertEquals($supplier->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $supplier = Supplier::factory()->create();
        $updateData = Supplier::factory()->make()->toArray();

        $result = $this->repository->update($supplier->id, $updateData);

        $this->assertInstanceOf(Supplier::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $supplier = Supplier::factory()->create();

        $result = $this->repository->delete($supplier->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('suppliers', ['id' => $supplier->id]);
    }
}
