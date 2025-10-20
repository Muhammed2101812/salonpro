<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\CustomerCategory;
use App\Repositories\Contracts\CustomerCategoryRepositoryInterface;
use App\Services\CustomerCategoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerCategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerCategoryService $service;
    protected CustomerCategoryRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CustomerCategoryRepositoryInterface::class);
        $this->service = new CustomerCategoryService($this->repository);
    }

    public function test_can_get_all_customerCategorys(): void
    {
        CustomerCategory::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_customerCategorys(): void
    {
        CustomerCategory::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_customerCategory(): void
    {
        $data = CustomerCategory::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(CustomerCategory::class, $result);
        $this->assertDatabaseHas('customerCategorys', ['id' => $result->id]);
    }

    public function test_can_update_customerCategory(): void
    {
        $customerCategory = CustomerCategory::factory()->create();
        $updateData = CustomerCategory::factory()->make()->toArray();

        $result = $this->service->update($customerCategory->id, $updateData);

        $this->assertInstanceOf(CustomerCategory::class, $result);
    }

    public function test_can_delete_customerCategory(): void
    {
        $customerCategory = CustomerCategory::factory()->create();

        $result = $this->service->delete($customerCategory->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('customerCategorys', ['id' => $customerCategory->id]);
    }

    public function test_can_find_customerCategory_by_id(): void
    {
        $customerCategory = CustomerCategory::factory()->create();

        $result = $this->service->findById($customerCategory->id);

        $this->assertInstanceOf(CustomerCategory::class, $result);
        $this->assertEquals($customerCategory->id, $result->id);
    }
}
