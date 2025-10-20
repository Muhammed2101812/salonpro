<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\ServiceTemplate;
use App\Repositories\Eloquent\ServiceTemplateRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceTemplateRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceTemplateRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ServiceTemplateRepository(new ServiceTemplate());
    }

    public function test_can_get_all_records(): void
    {
        ServiceTemplate::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = ServiceTemplate::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(ServiceTemplate::class, $result);
        $this->assertDatabaseHas('serviceTemplates', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $serviceTemplate = ServiceTemplate::factory()->create();

        $result = $this->repository->find($serviceTemplate->id);

        $this->assertInstanceOf(ServiceTemplate::class, $result);
        $this->assertEquals($serviceTemplate->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $serviceTemplate = ServiceTemplate::factory()->create();
        $updateData = ServiceTemplate::factory()->make()->toArray();

        $result = $this->repository->update($serviceTemplate->id, $updateData);

        $this->assertInstanceOf(ServiceTemplate::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $serviceTemplate = ServiceTemplate::factory()->create();

        $result = $this->repository->delete($serviceTemplate->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('serviceTemplates', ['id' => $serviceTemplate->id]);
    }
}
