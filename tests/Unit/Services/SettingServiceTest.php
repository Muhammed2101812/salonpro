<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SettingService $service;
    protected SettingRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(SettingRepositoryInterface::class);
        $this->service = new SettingService($this->repository);
    }

    public function test_can_get_all_settings(): void
    {
        Setting::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_settings(): void
    {
        Setting::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_setting(): void
    {
        $data = Setting::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Setting::class, $result);
        $this->assertDatabaseHas('settings', ['id' => $result->id]);
    }

    public function test_can_update_setting(): void
    {
        $setting = Setting::factory()->create();
        $updateData = Setting::factory()->make()->toArray();

        $result = $this->service->update($setting->id, $updateData);

        $this->assertInstanceOf(Setting::class, $result);
    }

    public function test_can_delete_setting(): void
    {
        $setting = Setting::factory()->create();

        $result = $this->service->delete($setting->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('settings', ['id' => $setting->id]);
    }

    public function test_can_find_setting_by_id(): void
    {
        $setting = Setting::factory()->create();

        $result = $this->service->findById($setting->id);

        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals($setting->id, $result->id);
    }
}
