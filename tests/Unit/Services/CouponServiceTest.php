<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Coupon;
use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Services\CouponService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CouponService $service;
    protected CouponRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CouponRepositoryInterface::class);
        $this->service = new CouponService($this->repository);
    }

    public function test_can_get_all_coupons(): void
    {
        Coupon::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_coupons(): void
    {
        Coupon::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_coupon(): void
    {
        $data = Coupon::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertDatabaseHas('coupons', ['id' => $result->id]);
    }

    public function test_can_update_coupon(): void
    {
        $coupon = Coupon::factory()->create();
        $updateData = Coupon::factory()->make()->toArray();

        $result = $this->service->update($coupon->id, $updateData);

        $this->assertInstanceOf(Coupon::class, $result);
    }

    public function test_can_delete_coupon(): void
    {
        $coupon = Coupon::factory()->create();

        $result = $this->service->delete($coupon->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('coupons', ['id' => $coupon->id]);
    }

    public function test_can_find_coupon_by_id(): void
    {
        $coupon = Coupon::factory()->create();

        $result = $this->service->findById($coupon->id);

        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertEquals($coupon->id, $result->id);
    }
}
