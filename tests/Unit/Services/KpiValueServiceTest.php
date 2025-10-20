<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\KpiValue;
use App\Repositories\Contracts\KpiValueRepositoryInterface;
use App\Services\KpiValueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KpiValueServiceTest extends TestCase
{
    use RefreshDatabase;

    protected KpiValueService $service;
    protected KpiValueRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(KpiValueRepositoryInterface::class);
        $this->service = new KpiValueService($this->repository);
    }

    public function test_can_get_all_kpiValues(): void
    {
        KpiValue::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_kpiValues(): void
    {
        KpiValue::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_kpiValue(): void
    {
        $data = KpiValue::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(KpiValue::class, $result);
        $this->assertDatabaseHas('kpiValues', ['id' => $result->id]);
    }

    public function test_can_update_kpiValue(): void
    {
        $kpiValue = KpiValue::factory()->create();
        $updateData = KpiValue::factory()->make()->toArray();

        $result = $this->service->update($kpiValue->id, $updateData);

        $this->assertInstanceOf(KpiValue::class, $result);
    }

    public function test_can_delete_kpiValue(): void
    {
        $kpiValue = KpiValue::factory()->create();

        $result = $this->service->delete($kpiValue->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('kpiValues', ['id' => $kpiValue->id]);
    }

    public function test_can_find_kpiValue_by_id(): void
    {
        $kpiValue = KpiValue::factory()->create();

        $result = $this->service->findById($kpiValue->id);

        $this->assertInstanceOf(KpiValue::class, $result);
        $this->assertEquals($kpiValue->id, $result->id);
    }
}
