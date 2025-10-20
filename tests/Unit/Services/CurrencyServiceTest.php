<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Currency;
use App\Repositories\Contracts\CurrencyRepositoryInterface;
use App\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CurrencyService $service;
    protected CurrencyRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CurrencyRepositoryInterface::class);
        $this->service = new CurrencyService($this->repository);
    }

    public function test_can_get_all_currencys(): void
    {
        Currency::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_currencys(): void
    {
        Currency::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_currency(): void
    {
        $data = Currency::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Currency::class, $result);
        $this->assertDatabaseHas('currencys', ['id' => $result->id]);
    }

    public function test_can_update_currency(): void
    {
        $currency = Currency::factory()->create();
        $updateData = Currency::factory()->make()->toArray();

        $result = $this->service->update($currency->id, $updateData);

        $this->assertInstanceOf(Currency::class, $result);
    }

    public function test_can_delete_currency(): void
    {
        $currency = Currency::factory()->create();

        $result = $this->service->delete($currency->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('currencys', ['id' => $currency->id]);
    }

    public function test_can_find_currency_by_id(): void
    {
        $currency = Currency::factory()->create();

        $result = $this->service->findById($currency->id);

        $this->assertInstanceOf(Currency::class, $result);
        $this->assertEquals($currency->id, $result->id);
    }
}
