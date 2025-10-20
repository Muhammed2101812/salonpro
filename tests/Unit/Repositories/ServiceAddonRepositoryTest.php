<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\ServiceAddon;
use App\Repositories\Eloquent\ServiceAddonRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceAddonRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceAddonRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ServiceAddonRepository(new ServiceAddon());
    }

    public function test_can_get_all_records(): void
    {
        ServiceAddon::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = ServiceAddon::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(ServiceAddon::class, $result);
        $this->assertDatabaseHas('serviceAddons', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $serviceAddon = ServiceAddon::factory()->create();

        $result = $this->repository->find($serviceAddon->id);

        $this->assertInstanceOf(ServiceAddon::class, $result);
        $this->assertEquals($serviceAddon->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $serviceAddon = ServiceAddon::factory()->create();
        $updateData = ServiceAddon::factory()->make()->toArray();

        $result = $this->repository->update($serviceAddon->id, $updateData);

        $this->assertInstanceOf(ServiceAddon::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $serviceAddon = ServiceAddon::factory()->create();

        $result = $this->repository->delete($serviceAddon->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('serviceAddons', ['id' => $serviceAddon->id]);
    }
}
