<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\LoyaltyPoint;
use App\Repositories\Contracts\LoyaltyPointRepositoryInterface;
use App\Services\LoyaltyPointService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyPointServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LoyaltyPointService $service;
    protected LoyaltyPointRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(LoyaltyPointRepositoryInterface::class);
        $this->service = new LoyaltyPointService($this->repository);
    }

    public function test_can_get_all_loyaltyPoints(): void
    {
        LoyaltyPoint::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_loyaltyPoints(): void
    {
        LoyaltyPoint::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_loyaltyPoint(): void
    {
        $data = LoyaltyPoint::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(LoyaltyPoint::class, $result);
        $this->assertDatabaseHas('loyaltyPoints', ['id' => $result->id]);
    }

    public function test_can_update_loyaltyPoint(): void
    {
        $loyaltyPoint = LoyaltyPoint::factory()->create();
        $updateData = LoyaltyPoint::factory()->make()->toArray();

        $result = $this->service->update($loyaltyPoint->id, $updateData);

        $this->assertInstanceOf(LoyaltyPoint::class, $result);
    }

    public function test_can_delete_loyaltyPoint(): void
    {
        $loyaltyPoint = LoyaltyPoint::factory()->create();

        $result = $this->service->delete($loyaltyPoint->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('loyaltyPoints', ['id' => $loyaltyPoint->id]);
    }

    public function test_can_find_loyaltyPoint_by_id(): void
    {
        $loyaltyPoint = LoyaltyPoint::factory()->create();

        $result = $this->service->findById($loyaltyPoint->id);

        $this->assertInstanceOf(LoyaltyPoint::class, $result);
        $this->assertEquals($loyaltyPoint->id, $result->id);
    }
}
