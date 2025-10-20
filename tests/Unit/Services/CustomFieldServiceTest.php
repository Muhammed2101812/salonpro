<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\CustomField;
use App\Repositories\Contracts\CustomFieldRepositoryInterface;
use App\Services\CustomFieldService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomFieldServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomFieldService $service;
    protected CustomFieldRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CustomFieldRepositoryInterface::class);
        $this->service = new CustomFieldService($this->repository);
    }

    public function test_can_get_all_customFields(): void
    {
        CustomField::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_customFields(): void
    {
        CustomField::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_customField(): void
    {
        $data = CustomField::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(CustomField::class, $result);
        $this->assertDatabaseHas('customFields', ['id' => $result->id]);
    }

    public function test_can_update_customField(): void
    {
        $customField = CustomField::factory()->create();
        $updateData = CustomField::factory()->make()->toArray();

        $result = $this->service->update($customField->id, $updateData);

        $this->assertInstanceOf(CustomField::class, $result);
    }

    public function test_can_delete_customField(): void
    {
        $customField = CustomField::factory()->create();

        $result = $this->service->delete($customField->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('customFields', ['id' => $customField->id]);
    }

    public function test_can_find_customField_by_id(): void
    {
        $customField = CustomField::factory()->create();

        $result = $this->service->findById($customField->id);

        $this->assertInstanceOf(CustomField::class, $result);
        $this->assertEquals($customField->id, $result->id);
    }
}
