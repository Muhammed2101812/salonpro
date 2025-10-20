<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\CampaignStatistic;
use App\Repositories\Eloquent\CampaignStatisticRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignStatisticRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected CampaignStatisticRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CampaignStatisticRepository(new CampaignStatistic());
    }

    public function test_can_get_all_records(): void
    {
        CampaignStatistic::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = CampaignStatistic::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(CampaignStatistic::class, $result);
        $this->assertDatabaseHas('campaignStatistics', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $campaignStatistic = CampaignStatistic::factory()->create();

        $result = $this->repository->find($campaignStatistic->id);

        $this->assertInstanceOf(CampaignStatistic::class, $result);
        $this->assertEquals($campaignStatistic->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $campaignStatistic = CampaignStatistic::factory()->create();
        $updateData = CampaignStatistic::factory()->make()->toArray();

        $result = $this->repository->update($campaignStatistic->id, $updateData);

        $this->assertInstanceOf(CampaignStatistic::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $campaignStatistic = CampaignStatistic::factory()->create();

        $result = $this->repository->delete($campaignStatistic->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('campaignStatistics', ['id' => $campaignStatistic->id]);
    }
}
