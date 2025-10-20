<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\ServicePriceHistory;
use App\Repositories\Eloquent\ServicePriceHistoryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePriceHistoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ServicePriceHistoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ServicePriceHistoryRepository(new ServicePriceHistory());
    }

    public function test_can_get_all_records(): void
    {
        ServicePriceHistory::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = ServicePriceHistory::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(ServicePriceHistory::class, $result);
        $this->assertDatabaseHas('servicePriceHistorys', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $servicePriceHistory = ServicePriceHistory::factory()->create();

        $result = $this->repository->find($servicePriceHistory->id);

        $this->assertInstanceOf(ServicePriceHistory::class, $result);
        $this->assertEquals($servicePriceHistory->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $servicePriceHistory = ServicePriceHistory::factory()->create();
        $updateData = ServicePriceHistory::factory()->make()->toArray();

        $result = $this->repository->update($servicePriceHistory->id, $updateData);

        $this->assertInstanceOf(ServicePriceHistory::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $servicePriceHistory = ServicePriceHistory::factory()->create();

        $result = $this->repository->delete($servicePriceHistory->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('servicePriceHistorys', ['id' => $servicePriceHistory->id]);
    }
}
