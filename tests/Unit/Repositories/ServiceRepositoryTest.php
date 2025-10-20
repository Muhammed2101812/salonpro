<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Service;
use App\Repositories\Eloquent\ServiceRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ServiceRepository(new Service());
    }

    public function test_can_get_all_records(): void
    {
        Service::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = Service::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(Service::class, $result);
        $this->assertDatabaseHas('services', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $service = Service::factory()->create();

        $result = $this->repository->find($service->id);

        $this->assertInstanceOf(Service::class, $result);
        $this->assertEquals($service->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $service = Service::factory()->create();
        $updateData = Service::factory()->make()->toArray();

        $result = $this->repository->update($service->id, $updateData);

        $this->assertInstanceOf(Service::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $service = Service::factory()->create();

        $result = $this->repository->delete($service->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }
}
