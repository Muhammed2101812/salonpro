<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Template;
use App\Repositories\Contracts\TemplateRepositoryInterface;
use App\Services\ServiceTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceTemplateServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceTemplateService $service;
    protected TemplateRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(TemplateRepositoryInterface::class);
        $this->service = new ServiceTemplateService($this->repository);
    }

    public function test_can_get_all_templates(): void
    {
        Template::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_templates(): void
    {
        Template::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_template(): void
    {
        $data = Template::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Template::class, $result);
        $this->assertDatabaseHas('templates', ['id' => $result->id]);
    }

    public function test_can_update_template(): void
    {
        $template = Template::factory()->create();
        $updateData = Template::factory()->make()->toArray();

        $result = $this->service->update($template->id, $updateData);

        $this->assertInstanceOf(Template::class, $result);
    }

    public function test_can_delete_template(): void
    {
        $template = Template::factory()->create();

        $result = $this->service->delete($template->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('templates', ['id' => $template->id]);
    }

    public function test_can_find_template_by_id(): void
    {
        $template = Template::factory()->create();

        $result = $this->service->findById($template->id);

        $this->assertInstanceOf(Template::class, $result);
        $this->assertEquals($template->id, $result->id);
    }
}
