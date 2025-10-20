<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\DocumentTemplate;
use App\Repositories\Contracts\DocumentTemplateRepositoryInterface;
use App\Services\DocumentTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentTemplateServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DocumentTemplateService $service;
    protected DocumentTemplateRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(DocumentTemplateRepositoryInterface::class);
        $this->service = new DocumentTemplateService($this->repository);
    }

    public function test_can_get_all_documentTemplates(): void
    {
        DocumentTemplate::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_documentTemplates(): void
    {
        DocumentTemplate::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_documentTemplate(): void
    {
        $data = DocumentTemplate::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(DocumentTemplate::class, $result);
        $this->assertDatabaseHas('documentTemplates', ['id' => $result->id]);
    }

    public function test_can_update_documentTemplate(): void
    {
        $documentTemplate = DocumentTemplate::factory()->create();
        $updateData = DocumentTemplate::factory()->make()->toArray();

        $result = $this->service->update($documentTemplate->id, $updateData);

        $this->assertInstanceOf(DocumentTemplate::class, $result);
    }

    public function test_can_delete_documentTemplate(): void
    {
        $documentTemplate = DocumentTemplate::factory()->create();

        $result = $this->service->delete($documentTemplate->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('documentTemplates', ['id' => $documentTemplate->id]);
    }

    public function test_can_find_documentTemplate_by_id(): void
    {
        $documentTemplate = DocumentTemplate::factory()->create();

        $result = $this->service->findById($documentTemplate->id);

        $this->assertInstanceOf(DocumentTemplate::class, $result);
        $this->assertEquals($documentTemplate->id, $result->id);
    }
}
