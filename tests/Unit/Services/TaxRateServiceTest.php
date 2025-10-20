<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\TaxRate;
use App\Repositories\Contracts\TaxRateRepositoryInterface;
use App\Services\TaxRateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxRateServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TaxRateService $service;
    protected TaxRateRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(TaxRateRepositoryInterface::class);
        $this->service = new TaxRateService($this->repository);
    }

    public function test_can_get_all_taxRates(): void
    {
        TaxRate::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_taxRates(): void
    {
        TaxRate::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_taxRate(): void
    {
        $data = TaxRate::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(TaxRate::class, $result);
        $this->assertDatabaseHas('taxRates', ['id' => $result->id]);
    }

    public function test_can_update_taxRate(): void
    {
        $taxRate = TaxRate::factory()->create();
        $updateData = TaxRate::factory()->make()->toArray();

        $result = $this->service->update($taxRate->id, $updateData);

        $this->assertInstanceOf(TaxRate::class, $result);
    }

    public function test_can_delete_taxRate(): void
    {
        $taxRate = TaxRate::factory()->create();

        $result = $this->service->delete($taxRate->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('taxRates', ['id' => $taxRate->id]);
    }

    public function test_can_find_taxRate_by_id(): void
    {
        $taxRate = TaxRate::factory()->create();

        $result = $this->service->findById($taxRate->id);

        $this->assertInstanceOf(TaxRate::class, $result);
        $this->assertEquals($taxRate->id, $result->id);
    }
}
