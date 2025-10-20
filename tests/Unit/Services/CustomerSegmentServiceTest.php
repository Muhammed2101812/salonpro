<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\CustomerSegment;
use App\Repositories\Contracts\CustomerSegmentRepositoryInterface;
use App\Services\CustomerSegmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerSegmentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerSegmentService $service;
    protected CustomerSegmentRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CustomerSegmentRepositoryInterface::class);
        $this->service = new CustomerSegmentService($this->repository);
    }

    public function test_can_get_all_customerSegments(): void
    {
        CustomerSegment::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_customerSegments(): void
    {
        CustomerSegment::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_customerSegment(): void
    {
        $data = CustomerSegment::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(CustomerSegment::class, $result);
        $this->assertDatabaseHas('customerSegments', ['id' => $result->id]);
    }

    public function test_can_update_customerSegment(): void
    {
        $customerSegment = CustomerSegment::factory()->create();
        $updateData = CustomerSegment::factory()->make()->toArray();

        $result = $this->service->update($customerSegment->id, $updateData);

        $this->assertInstanceOf(CustomerSegment::class, $result);
    }

    public function test_can_delete_customerSegment(): void
    {
        $customerSegment = CustomerSegment::factory()->create();

        $result = $this->service->delete($customerSegment->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('customerSegments', ['id' => $customerSegment->id]);
    }

    public function test_can_find_customerSegment_by_id(): void
    {
        $customerSegment = CustomerSegment::factory()->create();

        $result = $this->service->findById($customerSegment->id);

        $this->assertInstanceOf(CustomerSegment::class, $result);
        $this->assertEquals($customerSegment->id, $result->id);
    }
}
