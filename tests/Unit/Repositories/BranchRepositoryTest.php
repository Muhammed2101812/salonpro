<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Branch;
use App\Repositories\Eloquent\BranchRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected BranchRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new BranchRepository(new Branch());
    }

    public function test_can_get_all_records(): void
    {
        Branch::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = Branch::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(Branch::class, $result);
        $this->assertDatabaseHas('branchs', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $branch = Branch::factory()->create();

        $result = $this->repository->find($branch->id);

        $this->assertInstanceOf(Branch::class, $result);
        $this->assertEquals($branch->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $branch = Branch::factory()->create();
        $updateData = Branch::factory()->make()->toArray();

        $result = $this->repository->update($branch->id, $updateData);

        $this->assertInstanceOf(Branch::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $branch = Branch::factory()->create();

        $result = $this->repository->delete($branch->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('branchs', ['id' => $branch->id]);
    }
}
