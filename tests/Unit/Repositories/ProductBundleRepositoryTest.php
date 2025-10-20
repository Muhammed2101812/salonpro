<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\ProductBundle;
use App\Repositories\Eloquent\ProductBundleRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductBundleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ProductBundleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProductBundleRepository(new ProductBundle());
    }

    public function test_can_get_all_records(): void
    {
        ProductBundle::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = ProductBundle::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(ProductBundle::class, $result);
        $this->assertDatabaseHas('productBundles', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $productBundle = ProductBundle::factory()->create();

        $result = $this->repository->find($productBundle->id);

        $this->assertInstanceOf(ProductBundle::class, $result);
        $this->assertEquals($productBundle->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $productBundle = ProductBundle::factory()->create();
        $updateData = ProductBundle::factory()->make()->toArray();

        $result = $this->repository->update($productBundle->id, $updateData);

        $this->assertInstanceOf(ProductBundle::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $productBundle = ProductBundle::factory()->create();

        $result = $this->repository->delete($productBundle->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('productBundles', ['id' => $productBundle->id]);
    }
}
