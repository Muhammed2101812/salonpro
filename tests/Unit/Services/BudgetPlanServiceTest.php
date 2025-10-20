<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\BudgetPlan;
use App\Repositories\Contracts\BudgetPlanRepositoryInterface;
use App\Services\BudgetPlanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetPlanServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BudgetPlanService $service;
    protected BudgetPlanRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(BudgetPlanRepositoryInterface::class);
        $this->service = new BudgetPlanService($this->repository);
    }

    public function test_can_get_all_budgetPlans(): void
    {
        BudgetPlan::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_budgetPlans(): void
    {
        BudgetPlan::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_budgetPlan(): void
    {
        $data = BudgetPlan::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(BudgetPlan::class, $result);
        $this->assertDatabaseHas('budgetPlans', ['id' => $result->id]);
    }

    public function test_can_update_budgetPlan(): void
    {
        $budgetPlan = BudgetPlan::factory()->create();
        $updateData = BudgetPlan::factory()->make()->toArray();

        $result = $this->service->update($budgetPlan->id, $updateData);

        $this->assertInstanceOf(BudgetPlan::class, $result);
    }

    public function test_can_delete_budgetPlan(): void
    {
        $budgetPlan = BudgetPlan::factory()->create();

        $result = $this->service->delete($budgetPlan->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('budgetPlans', ['id' => $budgetPlan->id]);
    }

    public function test_can_find_budgetPlan_by_id(): void
    {
        $budgetPlan = BudgetPlan::factory()->create();

        $result = $this->service->findById($budgetPlan->id);

        $this->assertInstanceOf(BudgetPlan::class, $result);
        $this->assertEquals($budgetPlan->id, $result->id);
    }
}
