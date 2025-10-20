<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Branch;
use App\Repositories\Contracts\BranchRepositoryInterface;
use App\Services\BranchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BranchService $service;
    protected BranchRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(BranchRepositoryInterface::class);
        $this->service = new BranchService($this->repository);
    }

    public function test_can_get_all_branchs(): void
    {
        Branch::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_branchs(): void
    {
        Branch::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_branch(): void
    {
        $data = Branch::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Branch::class, $result);
        $this->assertDatabaseHas('branchs', ['id' => $result->id]);
    }

    public function test_can_update_branch(): void
    {
        $branch = Branch::factory()->create();
        $updateData = Branch::factory()->make()->toArray();

        $result = $this->service->update($branch->id, $updateData);

        $this->assertInstanceOf(Branch::class, $result);
    }

    public function test_can_delete_branch(): void
    {
        $branch = Branch::factory()->create();

        $result = $this->service->delete($branch->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('branchs', ['id' => $branch->id]);
    }

    public function test_can_find_branch_by_id(): void
    {
        $branch = Branch::factory()->create();

        $result = $this->service->findById($branch->id);

        $this->assertInstanceOf(Branch::class, $result);
        $this->assertEquals($branch->id, $result->id);
    }
}
