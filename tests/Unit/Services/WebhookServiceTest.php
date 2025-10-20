<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Webhook;
use App\Repositories\Contracts\WebhookRepositoryInterface;
use App\Services\WebhookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WebhookService $service;
    protected WebhookRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(WebhookRepositoryInterface::class);
        $this->service = new WebhookService($this->repository);
    }

    public function test_can_get_all_webhooks(): void
    {
        Webhook::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_webhooks(): void
    {
        Webhook::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_webhook(): void
    {
        $data = Webhook::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Webhook::class, $result);
        $this->assertDatabaseHas('webhooks', ['id' => $result->id]);
    }

    public function test_can_update_webhook(): void
    {
        $webhook = Webhook::factory()->create();
        $updateData = Webhook::factory()->make()->toArray();

        $result = $this->service->update($webhook->id, $updateData);

        $this->assertInstanceOf(Webhook::class, $result);
    }

    public function test_can_delete_webhook(): void
    {
        $webhook = Webhook::factory()->create();

        $result = $this->service->delete($webhook->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('webhooks', ['id' => $webhook->id]);
    }

    public function test_can_find_webhook_by_id(): void
    {
        $webhook = Webhook::factory()->create();

        $result = $this->service->findById($webhook->id);

        $this->assertInstanceOf(Webhook::class, $result);
        $this->assertEquals($webhook->id, $result->id);
    }
}
