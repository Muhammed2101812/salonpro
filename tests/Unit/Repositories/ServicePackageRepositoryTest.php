<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\ServicePackage;
use App\Repositories\Eloquent\ServicePackageRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePackageRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ServicePackageRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ServicePackageRepository(new ServicePackage());
    }

    public function test_can_get_all_records(): void
    {
        ServicePackage::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = ServicePackage::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(ServicePackage::class, $result);
        $this->assertDatabaseHas('servicePackages', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $servicePackage = ServicePackage::factory()->create();

        $result = $this->repository->find($servicePackage->id);

        $this->assertInstanceOf(ServicePackage::class, $result);
        $this->assertEquals($servicePackage->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $servicePackage = ServicePackage::factory()->create();
        $updateData = ServicePackage::factory()->make()->toArray();

        $result = $this->repository->update($servicePackage->id, $updateData);

        $this->assertInstanceOf(ServicePackage::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $servicePackage = ServicePackage::factory()->create();

        $result = $this->repository->delete($servicePackage->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('servicePackages', ['id' => $servicePackage->id]);
    }
}
