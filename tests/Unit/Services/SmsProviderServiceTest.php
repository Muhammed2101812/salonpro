<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\SmsProvider;
use App\Repositories\Contracts\SmsProviderRepositoryInterface;
use App\Services\SmsProviderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmsProviderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SmsProviderService $service;
    protected SmsProviderRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(SmsProviderRepositoryInterface::class);
        $this->service = new SmsProviderService($this->repository);
    }

    public function test_can_get_all_smsProviders(): void
    {
        SmsProvider::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_smsProviders(): void
    {
        SmsProvider::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_smsProvider(): void
    {
        $data = SmsProvider::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(SmsProvider::class, $result);
        $this->assertDatabaseHas('smsProviders', ['id' => $result->id]);
    }

    public function test_can_update_smsProvider(): void
    {
        $smsProvider = SmsProvider::factory()->create();
        $updateData = SmsProvider::factory()->make()->toArray();

        $result = $this->service->update($smsProvider->id, $updateData);

        $this->assertInstanceOf(SmsProvider::class, $result);
    }

    public function test_can_delete_smsProvider(): void
    {
        $smsProvider = SmsProvider::factory()->create();

        $result = $this->service->delete($smsProvider->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('smsProviders', ['id' => $smsProvider->id]);
    }

    public function test_can_find_smsProvider_by_id(): void
    {
        $smsProvider = SmsProvider::factory()->create();

        $result = $this->service->findById($smsProvider->id);

        $this->assertInstanceOf(SmsProvider::class, $result);
        $this->assertEquals($smsProvider->id, $result->id);
    }
}
