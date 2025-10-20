<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\MarketingCampaign;
use App\Repositories\Contracts\MarketingCampaignRepositoryInterface;
use App\Services\MarketingCampaignService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingCampaignServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MarketingCampaignService $service;
    protected MarketingCampaignRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(MarketingCampaignRepositoryInterface::class);
        $this->service = new MarketingCampaignService($this->repository);
    }

    public function test_can_get_all_marketingCampaigns(): void
    {
        MarketingCampaign::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_marketingCampaigns(): void
    {
        MarketingCampaign::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_marketingCampaign(): void
    {
        $data = MarketingCampaign::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(MarketingCampaign::class, $result);
        $this->assertDatabaseHas('marketingCampaigns', ['id' => $result->id]);
    }

    public function test_can_update_marketingCampaign(): void
    {
        $marketingCampaign = MarketingCampaign::factory()->create();
        $updateData = MarketingCampaign::factory()->make()->toArray();

        $result = $this->service->update($marketingCampaign->id, $updateData);

        $this->assertInstanceOf(MarketingCampaign::class, $result);
    }

    public function test_can_delete_marketingCampaign(): void
    {
        $marketingCampaign = MarketingCampaign::factory()->create();

        $result = $this->service->delete($marketingCampaign->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('marketingCampaigns', ['id' => $marketingCampaign->id]);
    }

    public function test_can_find_marketingCampaign_by_id(): void
    {
        $marketingCampaign = MarketingCampaign::factory()->create();

        $result = $this->service->findById($marketingCampaign->id);

        $this->assertInstanceOf(MarketingCampaign::class, $result);
        $this->assertEquals($marketingCampaign->id, $result->id);
    }
}
