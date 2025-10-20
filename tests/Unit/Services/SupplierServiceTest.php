<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Services\SupplierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SupplierService $service;
    protected SupplierRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(SupplierRepositoryInterface::class);
        $this->service = new SupplierService($this->repository);
    }

    public function test_can_get_all_suppliers(): void
    {
        Supplier::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_suppliers(): void
    {
        Supplier::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_supplier(): void
    {
        $data = Supplier::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Supplier::class, $result);
        $this->assertDatabaseHas('suppliers', ['id' => $result->id]);
    }

    public function test_can_update_supplier(): void
    {
        $supplier = Supplier::factory()->create();
        $updateData = Supplier::factory()->make()->toArray();

        $result = $this->service->update($supplier->id, $updateData);

        $this->assertInstanceOf(Supplier::class, $result);
    }

    public function test_can_delete_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $result = $this->service->delete($supplier->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('suppliers', ['id' => $supplier->id]);
    }

    public function test_can_find_supplier_by_id(): void
    {
        $supplier = Supplier::factory()->create();

        $result = $this->service->findById($supplier->id);

        $this->assertInstanceOf(Supplier::class, $result);
        $this->assertEquals($supplier->id, $result->id);
    }
}
