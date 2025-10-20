<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Addon;
use App\Repositories\Contracts\AddonRepositoryInterface;
use App\Services\ServiceAddonService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceAddonServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceAddonService $service;
    protected AddonRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AddonRepositoryInterface::class);
        $this->service = new ServiceAddonService($this->repository);
    }

    public function test_can_get_all_addons(): void
    {
        Addon::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_addons(): void
    {
        Addon::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_addon(): void
    {
        $data = Addon::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Addon::class, $result);
        $this->assertDatabaseHas('addons', ['id' => $result->id]);
    }

    public function test_can_update_addon(): void
    {
        $addon = Addon::factory()->create();
        $updateData = Addon::factory()->make()->toArray();

        $result = $this->service->update($addon->id, $updateData);

        $this->assertInstanceOf(Addon::class, $result);
    }

    public function test_can_delete_addon(): void
    {
        $addon = Addon::factory()->create();

        $result = $this->service->delete($addon->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('addons', ['id' => $addon->id]);
    }

    public function test_can_find_addon_by_id(): void
    {
        $addon = Addon::factory()->create();

        $result = $this->service->findById($addon->id);

        $this->assertInstanceOf(Addon::class, $result);
        $this->assertEquals($addon->id, $result->id);
    }
}
