<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\CustomerTag;
use App\Repositories\Eloquent\CustomerTagRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTagRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerTagRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CustomerTagRepository(new CustomerTag());
    }

    public function test_can_get_all_records(): void
    {
        CustomerTag::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = CustomerTag::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(CustomerTag::class, $result);
        $this->assertDatabaseHas('customerTags', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $customerTag = CustomerTag::factory()->create();

        $result = $this->repository->find($customerTag->id);

        $this->assertInstanceOf(CustomerTag::class, $result);
        $this->assertEquals($customerTag->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $customerTag = CustomerTag::factory()->create();
        $updateData = CustomerTag::factory()->make()->toArray();

        $result = $this->repository->update($customerTag->id, $updateData);

        $this->assertInstanceOf(CustomerTag::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $customerTag = CustomerTag::factory()->create();

        $result = $this->repository->delete($customerTag->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('customerTags', ['id' => $customerTag->id]);
    }
}
