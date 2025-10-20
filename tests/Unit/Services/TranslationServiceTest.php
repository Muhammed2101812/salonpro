<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Translation;
use App\Repositories\Contracts\TranslationRepositoryInterface;
use App\Services\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TranslationService $service;
    protected TranslationRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(TranslationRepositoryInterface::class);
        $this->service = new TranslationService($this->repository);
    }

    public function test_can_get_all_translations(): void
    {
        Translation::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_translations(): void
    {
        Translation::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_translation(): void
    {
        $data = Translation::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Translation::class, $result);
        $this->assertDatabaseHas('translations', ['id' => $result->id]);
    }

    public function test_can_update_translation(): void
    {
        $translation = Translation::factory()->create();
        $updateData = Translation::factory()->make()->toArray();

        $result = $this->service->update($translation->id, $updateData);

        $this->assertInstanceOf(Translation::class, $result);
    }

    public function test_can_delete_translation(): void
    {
        $translation = Translation::factory()->create();

        $result = $this->service->delete($translation->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('translations', ['id' => $translation->id]);
    }

    public function test_can_find_translation_by_id(): void
    {
        $translation = Translation::factory()->create();

        $result = $this->service->findById($translation->id);

        $this->assertInstanceOf(Translation::class, $result);
        $this->assertEquals($translation->id, $result->id);
    }
}
