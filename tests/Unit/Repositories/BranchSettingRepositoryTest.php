<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\BranchSetting;
use App\Repositories\Eloquent\BranchSettingRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchSettingRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected BranchSettingRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new BranchSettingRepository(new BranchSetting());
    }

    public function test_can_get_all_records(): void
    {
        BranchSetting::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = BranchSetting::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(BranchSetting::class, $result);
        $this->assertDatabaseHas('branchSettings', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $branchSetting = BranchSetting::factory()->create();

        $result = $this->repository->find($branchSetting->id);

        $this->assertInstanceOf(BranchSetting::class, $result);
        $this->assertEquals($branchSetting->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $branchSetting = BranchSetting::factory()->create();
        $updateData = BranchSetting::factory()->make()->toArray();

        $result = $this->repository->update($branchSetting->id, $updateData);

        $this->assertInstanceOf(BranchSetting::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $branchSetting = BranchSetting::factory()->create();

        $result = $this->repository->delete($branchSetting->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('branchSettings', ['id' => $branchSetting->id]);
    }
}
