<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\ServiceRequirement;
use App\Repositories\Eloquent\ServiceRequirementRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRequirementRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceRequirementRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ServiceRequirementRepository(new ServiceRequirement());
    }

    public function test_can_get_all_records(): void
    {
        ServiceRequirement::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = ServiceRequirement::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(ServiceRequirement::class, $result);
        $this->assertDatabaseHas('serviceRequirements', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $serviceRequirement = ServiceRequirement::factory()->create();

        $result = $this->repository->find($serviceRequirement->id);

        $this->assertInstanceOf(ServiceRequirement::class, $result);
        $this->assertEquals($serviceRequirement->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $serviceRequirement = ServiceRequirement::factory()->create();
        $updateData = ServiceRequirement::factory()->make()->toArray();

        $result = $this->repository->update($serviceRequirement->id, $updateData);

        $this->assertInstanceOf(ServiceRequirement::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $serviceRequirement = ServiceRequirement::factory()->create();

        $result = $this->repository->delete($serviceRequirement->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('serviceRequirements', ['id' => $serviceRequirement->id]);
    }
}
