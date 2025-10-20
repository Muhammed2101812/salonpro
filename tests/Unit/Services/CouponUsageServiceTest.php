<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\CouponUsage;
use App\Repositories\Contracts\CouponUsageRepositoryInterface;
use App\Services\CouponUsageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponUsageServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CouponUsageService $service;
    protected CouponUsageRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CouponUsageRepositoryInterface::class);
        $this->service = new CouponUsageService($this->repository);
    }

    public function test_can_get_all_couponUsages(): void
    {
        CouponUsage::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_couponUsages(): void
    {
        CouponUsage::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_couponUsage(): void
    {
        $data = CouponUsage::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(CouponUsage::class, $result);
        $this->assertDatabaseHas('couponUsages', ['id' => $result->id]);
    }

    public function test_can_update_couponUsage(): void
    {
        $couponUsage = CouponUsage::factory()->create();
        $updateData = CouponUsage::factory()->make()->toArray();

        $result = $this->service->update($couponUsage->id, $updateData);

        $this->assertInstanceOf(CouponUsage::class, $result);
    }

    public function test_can_delete_couponUsage(): void
    {
        $couponUsage = CouponUsage::factory()->create();

        $result = $this->service->delete($couponUsage->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('couponUsages', ['id' => $couponUsage->id]);
    }

    public function test_can_find_couponUsage_by_id(): void
    {
        $couponUsage = CouponUsage::factory()->create();

        $result = $this->service->findById($couponUsage->id);

        $this->assertInstanceOf(CouponUsage::class, $result);
        $this->assertEquals($couponUsage->id, $result->id);
    }
}
