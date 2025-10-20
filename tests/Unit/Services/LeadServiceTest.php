<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Lead;
use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Services\LeadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LeadService $service;
    protected LeadRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(LeadRepositoryInterface::class);
        $this->service = new LeadService($this->repository);
    }

    public function test_can_get_all_leads(): void
    {
        Lead::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_leads(): void
    {
        Lead::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_lead(): void
    {
        $data = Lead::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Lead::class, $result);
        $this->assertDatabaseHas('leads', ['id' => $result->id]);
    }

    public function test_can_update_lead(): void
    {
        $lead = Lead::factory()->create();
        $updateData = Lead::factory()->make()->toArray();

        $result = $this->service->update($lead->id, $updateData);

        $this->assertInstanceOf(Lead::class, $result);
    }

    public function test_can_delete_lead(): void
    {
        $lead = Lead::factory()->create();

        $result = $this->service->delete($lead->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('leads', ['id' => $lead->id]);
    }

    public function test_can_find_lead_by_id(): void
    {
        $lead = Lead::factory()->create();

        $result = $this->service->findById($lead->id);

        $this->assertInstanceOf(Lead::class, $result);
        $this->assertEquals($lead->id, $result->id);
    }
}
