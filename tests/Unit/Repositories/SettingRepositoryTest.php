<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Setting;
use App\Repositories\Eloquent\SettingRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected SettingRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new SettingRepository(new Setting());
    }

    public function test_can_get_all_records(): void
    {
        Setting::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = Setting::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(Setting::class, $result);
        $this->assertDatabaseHas('settings', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $setting = Setting::factory()->create();

        $result = $this->repository->find($setting->id);

        $this->assertInstanceOf(Setting::class, $result);
        $this->assertEquals($setting->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $setting = Setting::factory()->create();
        $updateData = Setting::factory()->make()->toArray();

        $result = $this->repository->update($setting->id, $updateData);

        $this->assertInstanceOf(Setting::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $setting = Setting::factory()->create();

        $result = $this->repository->delete($setting->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('settings', ['id' => $setting->id]);
    }
}
