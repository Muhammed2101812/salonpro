<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Package;
use App\Repositories\Contracts\PackageRepositoryInterface;
use App\Services\ServicePackageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePackageServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ServicePackageService $service;
    protected PackageRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(PackageRepositoryInterface::class);
        $this->service = new ServicePackageService($this->repository);
    }

    public function test_can_get_all_packages(): void
    {
        Package::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_packages(): void
    {
        Package::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_package(): void
    {
        $data = Package::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Package::class, $result);
        $this->assertDatabaseHas('packages', ['id' => $result->id]);
    }

    public function test_can_update_package(): void
    {
        $package = Package::factory()->create();
        $updateData = Package::factory()->make()->toArray();

        $result = $this->service->update($package->id, $updateData);

        $this->assertInstanceOf(Package::class, $result);
    }

    public function test_can_delete_package(): void
    {
        $package = Package::factory()->create();

        $result = $this->service->delete($package->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('packages', ['id' => $package->id]);
    }

    public function test_can_find_package_by_id(): void
    {
        $package = Package::factory()->create();

        $result = $this->service->findById($package->id);

        $this->assertInstanceOf(Package::class, $result);
        $this->assertEquals($package->id, $result->id);
    }
}
