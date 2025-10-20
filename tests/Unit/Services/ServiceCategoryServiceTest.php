<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\ServiceCategoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceCategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceCategoryService $service;
    protected CategoryRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CategoryRepositoryInterface::class);
        $this->service = new ServiceCategoryService($this->repository);
    }

    public function test_can_get_all_categorys(): void
    {
        Category::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_categorys(): void
    {
        Category::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_category(): void
    {
        $data = Category::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Category::class, $result);
        $this->assertDatabaseHas('categorys', ['id' => $result->id]);
    }

    public function test_can_update_category(): void
    {
        $category = Category::factory()->create();
        $updateData = Category::factory()->make()->toArray();

        $result = $this->service->update($category->id, $updateData);

        $this->assertInstanceOf(Category::class, $result);
    }

    public function test_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $result = $this->service->delete($category->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('categorys', ['id' => $category->id]);
    }

    public function test_can_find_category_by_id(): void
    {
        $category = Category::factory()->create();

        $result = $this->service->findById($category->id);

        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals($category->id, $result->id);
    }
}
