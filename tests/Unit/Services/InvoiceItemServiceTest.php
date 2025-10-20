<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\InvoiceItem;
use App\Repositories\Contracts\InvoiceItemRepositoryInterface;
use App\Services\InvoiceItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceItemServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InvoiceItemService $service;
    protected InvoiceItemRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(InvoiceItemRepositoryInterface::class);
        $this->service = new InvoiceItemService($this->repository);
    }

    public function test_can_get_all_invoiceItems(): void
    {
        InvoiceItem::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_invoiceItems(): void
    {
        InvoiceItem::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_invoiceItem(): void
    {
        $data = InvoiceItem::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(InvoiceItem::class, $result);
        $this->assertDatabaseHas('invoiceItems', ['id' => $result->id]);
    }

    public function test_can_update_invoiceItem(): void
    {
        $invoiceItem = InvoiceItem::factory()->create();
        $updateData = InvoiceItem::factory()->make()->toArray();

        $result = $this->service->update($invoiceItem->id, $updateData);

        $this->assertInstanceOf(InvoiceItem::class, $result);
    }

    public function test_can_delete_invoiceItem(): void
    {
        $invoiceItem = InvoiceItem::factory()->create();

        $result = $this->service->delete($invoiceItem->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('invoiceItems', ['id' => $invoiceItem->id]);
    }

    public function test_can_find_invoiceItem_by_id(): void
    {
        $invoiceItem = InvoiceItem::factory()->create();

        $result = $this->service->findById($invoiceItem->id);

        $this->assertInstanceOf(InvoiceItem::class, $result);
        $this->assertEquals($invoiceItem->id, $result->id);
    }
}
