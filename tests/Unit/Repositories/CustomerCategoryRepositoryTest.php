<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\CustomerCategory;
use App\Repositories\Eloquent\CustomerCategoryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerCategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerCategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CustomerCategoryRepository(new CustomerCategory());
    }

    public function test_can_get_all_records(): void
    {
        CustomerCategory::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = CustomerCategory::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(CustomerCategory::class, $result);
        $this->assertDatabaseHas('customerCategorys', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $customerCategory = CustomerCategory::factory()->create();

        $result = $this->repository->find($customerCategory->id);

        $this->assertInstanceOf(CustomerCategory::class, $result);
        $this->assertEquals($customerCategory->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $customerCategory = CustomerCategory::factory()->create();
        $updateData = CustomerCategory::factory()->make()->toArray();

        $result = $this->repository->update($customerCategory->id, $updateData);

        $this->assertInstanceOf(CustomerCategory::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $customerCategory = CustomerCategory::factory()->create();

        $result = $this->repository->delete($customerCategory->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('customerCategorys', ['id' => $customerCategory->id]);
    }
}
