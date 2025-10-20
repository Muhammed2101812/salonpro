<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Product;
use App\Repositories\Eloquent\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ProductRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProductRepository(new Product());
    }

    public function test_can_get_all_records(): void
    {
        Product::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = Product::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertDatabaseHas('products', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $product = Product::factory()->create();

        $result = $this->repository->find($product->id);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($product->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $product = Product::factory()->create();
        $updateData = Product::factory()->make()->toArray();

        $result = $this->repository->update($product->id, $updateData);

        $this->assertInstanceOf(Product::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $product = Product::factory()->create();

        $result = $this->repository->delete($product->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }
}
