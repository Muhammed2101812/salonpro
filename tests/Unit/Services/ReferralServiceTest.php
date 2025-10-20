<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Referral;
use App\Repositories\Contracts\ReferralRepositoryInterface;
use App\Services\ReferralService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferralServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ReferralService $service;
    protected ReferralRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ReferralRepositoryInterface::class);
        $this->service = new ReferralService($this->repository);
    }

    public function test_can_get_all_referrals(): void
    {
        Referral::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_referrals(): void
    {
        Referral::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_referral(): void
    {
        $data = Referral::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Referral::class, $result);
        $this->assertDatabaseHas('referrals', ['id' => $result->id]);
    }

    public function test_can_update_referral(): void
    {
        $referral = Referral::factory()->create();
        $updateData = Referral::factory()->make()->toArray();

        $result = $this->service->update($referral->id, $updateData);

        $this->assertInstanceOf(Referral::class, $result);
    }

    public function test_can_delete_referral(): void
    {
        $referral = Referral::factory()->create();

        $result = $this->service->delete($referral->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('referrals', ['id' => $referral->id]);
    }

    public function test_can_find_referral_by_id(): void
    {
        $referral = Referral::factory()->create();

        $result = $this->service->findById($referral->id);

        $this->assertInstanceOf(Referral::class, $result);
        $this->assertEquals($referral->id, $result->id);
    }
}
