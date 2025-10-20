<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\LeadActivity;
use App\Repositories\Contracts\LeadActivityRepositoryInterface;
use App\Services\LeadActivityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadActivityServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LeadActivityService $service;
    protected LeadActivityRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(LeadActivityRepositoryInterface::class);
        $this->service = new LeadActivityService($this->repository);
    }

    public function test_can_get_all_leadActivitys(): void
    {
        LeadActivity::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_leadActivitys(): void
    {
        LeadActivity::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_leadActivity(): void
    {
        $data = LeadActivity::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(LeadActivity::class, $result);
        $this->assertDatabaseHas('leadActivitys', ['id' => $result->id]);
    }

    public function test_can_update_leadActivity(): void
    {
        $leadActivity = LeadActivity::factory()->create();
        $updateData = LeadActivity::factory()->make()->toArray();

        $result = $this->service->update($leadActivity->id, $updateData);

        $this->assertInstanceOf(LeadActivity::class, $result);
    }

    public function test_can_delete_leadActivity(): void
    {
        $leadActivity = LeadActivity::factory()->create();

        $result = $this->service->delete($leadActivity->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('leadActivitys', ['id' => $leadActivity->id]);
    }

    public function test_can_find_leadActivity_by_id(): void
    {
        $leadActivity = LeadActivity::factory()->create();

        $result = $this->service->findById($leadActivity->id);

        $this->assertInstanceOf(LeadActivity::class, $result);
        $this->assertEquals($leadActivity->id, $result->id);
    }
}
