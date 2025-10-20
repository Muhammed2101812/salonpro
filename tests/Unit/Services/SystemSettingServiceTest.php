<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\SystemSetting;
use App\Repositories\Contracts\SystemSettingRepositoryInterface;
use App\Services\SystemSettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemSettingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SystemSettingService $service;
    protected SystemSettingRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(SystemSettingRepositoryInterface::class);
        $this->service = new SystemSettingService($this->repository);
    }

    public function test_can_get_all_systemSettings(): void
    {
        SystemSetting::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_systemSettings(): void
    {
        SystemSetting::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_systemSetting(): void
    {
        $data = SystemSetting::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(SystemSetting::class, $result);
        $this->assertDatabaseHas('systemSettings', ['id' => $result->id]);
    }

    public function test_can_update_systemSetting(): void
    {
        $systemSetting = SystemSetting::factory()->create();
        $updateData = SystemSetting::factory()->make()->toArray();

        $result = $this->service->update($systemSetting->id, $updateData);

        $this->assertInstanceOf(SystemSetting::class, $result);
    }

    public function test_can_delete_systemSetting(): void
    {
        $systemSetting = SystemSetting::factory()->create();

        $result = $this->service->delete($systemSetting->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('systemSettings', ['id' => $systemSetting->id]);
    }

    public function test_can_find_systemSetting_by_id(): void
    {
        $systemSetting = SystemSetting::factory()->create();

        $result = $this->service->findById($systemSetting->id);

        $this->assertInstanceOf(SystemSetting::class, $result);
        $this->assertEquals($systemSetting->id, $result->id);
    }
}
