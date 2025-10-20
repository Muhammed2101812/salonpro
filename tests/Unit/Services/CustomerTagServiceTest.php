<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\CustomerTag;
use App\Repositories\Contracts\CustomerTagRepositoryInterface;
use App\Services\CustomerTagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTagServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerTagService $service;
    protected CustomerTagRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CustomerTagRepositoryInterface::class);
        $this->service = new CustomerTagService($this->repository);
    }

    public function test_can_get_all_customerTags(): void
    {
        CustomerTag::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_customerTags(): void
    {
        CustomerTag::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_customerTag(): void
    {
        $data = CustomerTag::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(CustomerTag::class, $result);
        $this->assertDatabaseHas('customerTags', ['id' => $result->id]);
    }

    public function test_can_update_customerTag(): void
    {
        $customerTag = CustomerTag::factory()->create();
        $updateData = CustomerTag::factory()->make()->toArray();

        $result = $this->service->update($customerTag->id, $updateData);

        $this->assertInstanceOf(CustomerTag::class, $result);
    }

    public function test_can_delete_customerTag(): void
    {
        $customerTag = CustomerTag::factory()->create();

        $result = $this->service->delete($customerTag->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('customerTags', ['id' => $customerTag->id]);
    }

    public function test_can_find_customerTag_by_id(): void
    {
        $customerTag = CustomerTag::factory()->create();

        $result = $this->service->findById($customerTag->id);

        $this->assertInstanceOf(CustomerTag::class, $result);
        $this->assertEquals($customerTag->id, $result->id);
    }
}
