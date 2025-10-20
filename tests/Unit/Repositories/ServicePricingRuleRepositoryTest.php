<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\ServicePricingRule;
use App\Repositories\Eloquent\ServicePricingRuleRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePricingRuleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ServicePricingRuleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ServicePricingRuleRepository(new ServicePricingRule());
    }

    public function test_can_get_all_records(): void
    {
        ServicePricingRule::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = ServicePricingRule::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(ServicePricingRule::class, $result);
        $this->assertDatabaseHas('servicePricingRules', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $servicePricingRule = ServicePricingRule::factory()->create();

        $result = $this->repository->find($servicePricingRule->id);

        $this->assertInstanceOf(ServicePricingRule::class, $result);
        $this->assertEquals($servicePricingRule->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $servicePricingRule = ServicePricingRule::factory()->create();
        $updateData = ServicePricingRule::factory()->make()->toArray();

        $result = $this->repository->update($servicePricingRule->id, $updateData);

        $this->assertInstanceOf(ServicePricingRule::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $servicePricingRule = ServicePricingRule::factory()->create();

        $result = $this->repository->delete($servicePricingRule->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('servicePricingRules', ['id' => $servicePricingRule->id]);
    }
}
