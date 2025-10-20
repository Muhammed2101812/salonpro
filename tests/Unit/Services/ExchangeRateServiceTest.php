<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ExchangeRate;
use App\Repositories\Contracts\ExchangeRateRepositoryInterface;
use App\Services\ExchangeRateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExchangeRateServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ExchangeRateService $service;
    protected ExchangeRateRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ExchangeRateRepositoryInterface::class);
        $this->service = new ExchangeRateService($this->repository);
    }

    public function test_can_get_all_exchangeRates(): void
    {
        ExchangeRate::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_exchangeRates(): void
    {
        ExchangeRate::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_exchangeRate(): void
    {
        $data = ExchangeRate::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(ExchangeRate::class, $result);
        $this->assertDatabaseHas('exchangeRates', ['id' => $result->id]);
    }

    public function test_can_update_exchangeRate(): void
    {
        $exchangeRate = ExchangeRate::factory()->create();
        $updateData = ExchangeRate::factory()->make()->toArray();

        $result = $this->service->update($exchangeRate->id, $updateData);

        $this->assertInstanceOf(ExchangeRate::class, $result);
    }

    public function test_can_delete_exchangeRate(): void
    {
        $exchangeRate = ExchangeRate::factory()->create();

        $result = $this->service->delete($exchangeRate->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('exchangeRates', ['id' => $exchangeRate->id]);
    }

    public function test_can_find_exchangeRate_by_id(): void
    {
        $exchangeRate = ExchangeRate::factory()->create();

        $result = $this->service->findById($exchangeRate->id);

        $this->assertInstanceOf(ExchangeRate::class, $result);
        $this->assertEquals($exchangeRate->id, $result->id);
    }
}
