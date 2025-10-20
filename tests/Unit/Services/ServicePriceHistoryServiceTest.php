<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\PriceHistory;
use App\Repositories\Contracts\PriceHistoryRepositoryInterface;
use App\Services\ServicePriceHistoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePriceHistoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ServicePriceHistoryService $service;
    protected PriceHistoryRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(PriceHistoryRepositoryInterface::class);
        $this->service = new ServicePriceHistoryService($this->repository);
    }

    public function test_can_get_all_priceHistorys(): void
    {
        PriceHistory::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_priceHistorys(): void
    {
        PriceHistory::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_priceHistory(): void
    {
        $data = PriceHistory::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(PriceHistory::class, $result);
        $this->assertDatabaseHas('priceHistorys', ['id' => $result->id]);
    }

    public function test_can_update_priceHistory(): void
    {
        $priceHistory = PriceHistory::factory()->create();
        $updateData = PriceHistory::factory()->make()->toArray();

        $result = $this->service->update($priceHistory->id, $updateData);

        $this->assertInstanceOf(PriceHistory::class, $result);
    }

    public function test_can_delete_priceHistory(): void
    {
        $priceHistory = PriceHistory::factory()->create();

        $result = $this->service->delete($priceHistory->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('priceHistorys', ['id' => $priceHistory->id]);
    }

    public function test_can_find_priceHistory_by_id(): void
    {
        $priceHistory = PriceHistory::factory()->create();

        $result = $this->service->findById($priceHistory->id);

        $this->assertInstanceOf(PriceHistory::class, $result);
        $this->assertEquals($priceHistory->id, $result->id);
    }
}
