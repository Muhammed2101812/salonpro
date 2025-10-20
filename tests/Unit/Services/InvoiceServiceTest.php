<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Invoice;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InvoiceService $service;
    protected InvoiceRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(InvoiceRepositoryInterface::class);
        $this->service = new InvoiceService($this->repository);
    }

    public function test_can_get_all_invoices(): void
    {
        Invoice::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_invoices(): void
    {
        Invoice::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_invoice(): void
    {
        $data = Invoice::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Invoice::class, $result);
        $this->assertDatabaseHas('invoices', ['id' => $result->id]);
    }

    public function test_can_update_invoice(): void
    {
        $invoice = Invoice::factory()->create();
        $updateData = Invoice::factory()->make()->toArray();

        $result = $this->service->update($invoice->id, $updateData);

        $this->assertInstanceOf(Invoice::class, $result);
    }

    public function test_can_delete_invoice(): void
    {
        $invoice = Invoice::factory()->create();

        $result = $this->service->delete($invoice->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('invoices', ['id' => $invoice->id]);
    }

    public function test_can_find_invoice_by_id(): void
    {
        $invoice = Invoice::factory()->create();

        $result = $this->service->findById($invoice->id);

        $this->assertInstanceOf(Invoice::class, $result);
        $this->assertEquals($invoice->id, $result->id);
    }
}
