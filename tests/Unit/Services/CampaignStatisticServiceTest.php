<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\CampaignStatistic;
use App\Repositories\Contracts\CampaignStatisticRepositoryInterface;
use App\Services\CampaignStatisticService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignStatisticServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CampaignStatisticService $service;
    protected CampaignStatisticRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CampaignStatisticRepositoryInterface::class);
        $this->service = new CampaignStatisticService($this->repository);
    }

    public function test_can_get_all_campaignStatistics(): void
    {
        CampaignStatistic::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_campaignStatistics(): void
    {
        CampaignStatistic::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_campaignStatistic(): void
    {
        $data = CampaignStatistic::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(CampaignStatistic::class, $result);
        $this->assertDatabaseHas('campaignStatistics', ['id' => $result->id]);
    }

    public function test_can_update_campaignStatistic(): void
    {
        $campaignStatistic = CampaignStatistic::factory()->create();
        $updateData = CampaignStatistic::factory()->make()->toArray();

        $result = $this->service->update($campaignStatistic->id, $updateData);

        $this->assertInstanceOf(CampaignStatistic::class, $result);
    }

    public function test_can_delete_campaignStatistic(): void
    {
        $campaignStatistic = CampaignStatistic::factory()->create();

        $result = $this->service->delete($campaignStatistic->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('campaignStatistics', ['id' => $campaignStatistic->id]);
    }

    public function test_can_find_campaignStatistic_by_id(): void
    {
        $campaignStatistic = CampaignStatistic::factory()->create();

        $result = $this->service->findById($campaignStatistic->id);

        $this->assertInstanceOf(CampaignStatistic::class, $result);
        $this->assertEquals($campaignStatistic->id, $result->id);
    }
}
