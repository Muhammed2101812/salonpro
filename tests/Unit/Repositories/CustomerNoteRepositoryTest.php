<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\CustomerNote;
use App\Repositories\Eloquent\CustomerNoteRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerNoteRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerNoteRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CustomerNoteRepository(new CustomerNote());
    }

    public function test_can_get_all_records(): void
    {
        CustomerNote::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = CustomerNote::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(CustomerNote::class, $result);
        $this->assertDatabaseHas('customerNotes', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $customerNote = CustomerNote::factory()->create();

        $result = $this->repository->find($customerNote->id);

        $this->assertInstanceOf(CustomerNote::class, $result);
        $this->assertEquals($customerNote->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $customerNote = CustomerNote::factory()->create();
        $updateData = CustomerNote::factory()->make()->toArray();

        $result = $this->repository->update($customerNote->id, $updateData);

        $this->assertInstanceOf(CustomerNote::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $customerNote = CustomerNote::factory()->create();

        $result = $this->repository->delete($customerNote->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('customerNotes', ['id' => $customerNote->id]);
    }
}
