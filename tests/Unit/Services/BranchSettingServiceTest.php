<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\BranchSetting;
use App\Repositories\Contracts\BranchSettingRepositoryInterface;
use App\Services\BranchSettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchSettingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BranchSettingService $service;
    protected BranchSettingRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(BranchSettingRepositoryInterface::class);
        $this->service = new BranchSettingService($this->repository);
    }

    public function test_can_get_all_branchSettings(): void
    {
        BranchSetting::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_branchSettings(): void
    {
        BranchSetting::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_branchSetting(): void
    {
        $data = BranchSetting::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(BranchSetting::class, $result);
        $this->assertDatabaseHas('branchSettings', ['id' => $result->id]);
    }

    public function test_can_update_branchSetting(): void
    {
        $branchSetting = BranchSetting::factory()->create();
        $updateData = BranchSetting::factory()->make()->toArray();

        $result = $this->service->update($branchSetting->id, $updateData);

        $this->assertInstanceOf(BranchSetting::class, $result);
    }

    public function test_can_delete_branchSetting(): void
    {
        $branchSetting = BranchSetting::factory()->create();

        $result = $this->service->delete($branchSetting->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('branchSettings', ['id' => $branchSetting->id]);
    }

    public function test_can_find_branchSetting_by_id(): void
    {
        $branchSetting = BranchSetting::factory()->create();

        $result = $this->service->findById($branchSetting->id);

        $this->assertInstanceOf(BranchSetting::class, $result);
        $this->assertEquals($branchSetting->id, $result->id);
    }
}
