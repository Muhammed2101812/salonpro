<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\PricingRule;
use App\Repositories\Contracts\PricingRuleRepositoryInterface;
use App\Services\ServicePricingRuleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePricingRuleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ServicePricingRuleService $service;
    protected PricingRuleRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(PricingRuleRepositoryInterface::class);
        $this->service = new ServicePricingRuleService($this->repository);
    }

    public function test_can_get_all_pricingRules(): void
    {
        PricingRule::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_pricingRules(): void
    {
        PricingRule::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_pricingRule(): void
    {
        $data = PricingRule::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(PricingRule::class, $result);
        $this->assertDatabaseHas('pricingRules', ['id' => $result->id]);
    }

    public function test_can_update_pricingRule(): void
    {
        $pricingRule = PricingRule::factory()->create();
        $updateData = PricingRule::factory()->make()->toArray();

        $result = $this->service->update($pricingRule->id, $updateData);

        $this->assertInstanceOf(PricingRule::class, $result);
    }

    public function test_can_delete_pricingRule(): void
    {
        $pricingRule = PricingRule::factory()->create();

        $result = $this->service->delete($pricingRule->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('pricingRules', ['id' => $pricingRule->id]);
    }

    public function test_can_find_pricingRule_by_id(): void
    {
        $pricingRule = PricingRule::factory()->create();

        $result = $this->service->findById($pricingRule->id);

        $this->assertInstanceOf(PricingRule::class, $result);
        $this->assertEquals($pricingRule->id, $result->id);
    }
}
