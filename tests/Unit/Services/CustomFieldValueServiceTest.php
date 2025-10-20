<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\CustomFieldValue;
use App\Repositories\Contracts\CustomFieldValueRepositoryInterface;
use App\Services\CustomFieldValueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomFieldValueServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomFieldValueService $service;
    protected CustomFieldValueRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CustomFieldValueRepositoryInterface::class);
        $this->service = new CustomFieldValueService($this->repository);
    }

    public function test_can_get_all_customFieldValues(): void
    {
        CustomFieldValue::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_customFieldValues(): void
    {
        CustomFieldValue::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_customFieldValue(): void
    {
        $data = CustomFieldValue::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(CustomFieldValue::class, $result);
        $this->assertDatabaseHas('customFieldValues', ['id' => $result->id]);
    }

    public function test_can_update_customFieldValue(): void
    {
        $customFieldValue = CustomFieldValue::factory()->create();
        $updateData = CustomFieldValue::factory()->make()->toArray();

        $result = $this->service->update($customFieldValue->id, $updateData);

        $this->assertInstanceOf(CustomFieldValue::class, $result);
    }

    public function test_can_delete_customFieldValue(): void
    {
        $customFieldValue = CustomFieldValue::factory()->create();

        $result = $this->service->delete($customFieldValue->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('customFieldValues', ['id' => $customFieldValue->id]);
    }

    public function test_can_find_customFieldValue_by_id(): void
    {
        $customFieldValue = CustomFieldValue::factory()->create();

        $result = $this->service->findById($customFieldValue->id);

        $this->assertInstanceOf(CustomFieldValue::class, $result);
        $this->assertEquals($customFieldValue->id, $result->id);
    }
}
