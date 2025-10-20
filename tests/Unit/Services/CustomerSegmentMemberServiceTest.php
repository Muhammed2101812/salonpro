<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\CustomerSegmentMember;
use App\Repositories\Contracts\CustomerSegmentMemberRepositoryInterface;
use App\Services\CustomerSegmentMemberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerSegmentMemberServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerSegmentMemberService $service;
    protected CustomerSegmentMemberRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CustomerSegmentMemberRepositoryInterface::class);
        $this->service = new CustomerSegmentMemberService($this->repository);
    }

    public function test_can_get_all_customerSegmentMembers(): void
    {
        CustomerSegmentMember::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_customerSegmentMembers(): void
    {
        CustomerSegmentMember::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_customerSegmentMember(): void
    {
        $data = CustomerSegmentMember::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(CustomerSegmentMember::class, $result);
        $this->assertDatabaseHas('customerSegmentMembers', ['id' => $result->id]);
    }

    public function test_can_update_customerSegmentMember(): void
    {
        $customerSegmentMember = CustomerSegmentMember::factory()->create();
        $updateData = CustomerSegmentMember::factory()->make()->toArray();

        $result = $this->service->update($customerSegmentMember->id, $updateData);

        $this->assertInstanceOf(CustomerSegmentMember::class, $result);
    }

    public function test_can_delete_customerSegmentMember(): void
    {
        $customerSegmentMember = CustomerSegmentMember::factory()->create();

        $result = $this->service->delete($customerSegmentMember->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('customerSegmentMembers', ['id' => $customerSegmentMember->id]);
    }

    public function test_can_find_customerSegmentMember_by_id(): void
    {
        $customerSegmentMember = CustomerSegmentMember::factory()->create();

        $result = $this->service->findById($customerSegmentMember->id);

        $this->assertInstanceOf(CustomerSegmentMember::class, $result);
        $this->assertEquals($customerSegmentMember->id, $result->id);
    }
}
