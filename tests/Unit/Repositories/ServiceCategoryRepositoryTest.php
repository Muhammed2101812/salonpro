<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\ServiceCategory;
use App\Repositories\Eloquent\ServiceCategoryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceCategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceCategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ServiceCategoryRepository(new ServiceCategory());
    }

    public function test_can_get_all_records(): void
    {
        ServiceCategory::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = ServiceCategory::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(ServiceCategory::class, $result);
        $this->assertDatabaseHas('serviceCategorys', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $serviceCategory = ServiceCategory::factory()->create();

        $result = $this->repository->find($serviceCategory->id);

        $this->assertInstanceOf(ServiceCategory::class, $result);
        $this->assertEquals($serviceCategory->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $serviceCategory = ServiceCategory::factory()->create();
        $updateData = ServiceCategory::factory()->make()->toArray();

        $result = $this->repository->update($serviceCategory->id, $updateData);

        $this->assertInstanceOf(ServiceCategory::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $serviceCategory = ServiceCategory::factory()->create();

        $result = $this->repository->delete($serviceCategory->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('serviceCategorys', ['id' => $serviceCategory->id]);
    }
}
