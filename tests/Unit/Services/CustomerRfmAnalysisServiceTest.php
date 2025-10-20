<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\CustomerRfmAnalysis;
use App\Repositories\Contracts\CustomerRfmAnalysisRepositoryInterface;
use App\Services\CustomerRfmAnalysisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerRfmAnalysisServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerRfmAnalysisService $service;
    protected CustomerRfmAnalysisRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CustomerRfmAnalysisRepositoryInterface::class);
        $this->service = new CustomerRfmAnalysisService($this->repository);
    }

    public function test_can_get_all_customerRfmAnalysiss(): void
    {
        CustomerRfmAnalysis::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_customerRfmAnalysiss(): void
    {
        CustomerRfmAnalysis::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_customerRfmAnalysis(): void
    {
        $data = CustomerRfmAnalysis::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(CustomerRfmAnalysis::class, $result);
        $this->assertDatabaseHas('customerRfmAnalysiss', ['id' => $result->id]);
    }

    public function test_can_update_customerRfmAnalysis(): void
    {
        $customerRfmAnalysis = CustomerRfmAnalysis::factory()->create();
        $updateData = CustomerRfmAnalysis::factory()->make()->toArray();

        $result = $this->service->update($customerRfmAnalysis->id, $updateData);

        $this->assertInstanceOf(CustomerRfmAnalysis::class, $result);
    }

    public function test_can_delete_customerRfmAnalysis(): void
    {
        $customerRfmAnalysis = CustomerRfmAnalysis::factory()->create();

        $result = $this->service->delete($customerRfmAnalysis->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('customerRfmAnalysiss', ['id' => $customerRfmAnalysis->id]);
    }

    public function test_can_find_customerRfmAnalysis_by_id(): void
    {
        $customerRfmAnalysis = CustomerRfmAnalysis::factory()->create();

        $result = $this->service->findById($customerRfmAnalysis->id);

        $this->assertInstanceOf(CustomerRfmAnalysis::class, $result);
        $this->assertEquals($customerRfmAnalysis->id, $result->id);
    }
}
