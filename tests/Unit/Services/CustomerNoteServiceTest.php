<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\CustomerNote;
use App\Repositories\Contracts\CustomerNoteRepositoryInterface;
use App\Services\CustomerNoteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerNoteServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerNoteService $service;
    protected CustomerNoteRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CustomerNoteRepositoryInterface::class);
        $this->service = new CustomerNoteService($this->repository);
    }

    public function test_can_get_all_customerNotes(): void
    {
        CustomerNote::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_customerNotes(): void
    {
        CustomerNote::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_customerNote(): void
    {
        $data = CustomerNote::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(CustomerNote::class, $result);
        $this->assertDatabaseHas('customerNotes', ['id' => $result->id]);
    }

    public function test_can_update_customerNote(): void
    {
        $customerNote = CustomerNote::factory()->create();
        $updateData = CustomerNote::factory()->make()->toArray();

        $result = $this->service->update($customerNote->id, $updateData);

        $this->assertInstanceOf(CustomerNote::class, $result);
    }

    public function test_can_delete_customerNote(): void
    {
        $customerNote = CustomerNote::factory()->create();

        $result = $this->service->delete($customerNote->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('customerNotes', ['id' => $customerNote->id]);
    }

    public function test_can_find_customerNote_by_id(): void
    {
        $customerNote = CustomerNote::factory()->create();

        $result = $this->service->findById($customerNote->id);

        $this->assertInstanceOf(CustomerNote::class, $result);
        $this->assertEquals($customerNote->id, $result->id);
    }
}
