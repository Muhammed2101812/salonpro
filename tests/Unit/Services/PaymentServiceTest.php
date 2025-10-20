<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentService $service;
    protected PaymentRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(PaymentRepositoryInterface::class);
        $this->service = new PaymentService($this->repository);
    }

    public function test_can_get_all_payments(): void
    {
        Payment::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_payments(): void
    {
        Payment::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_payment(): void
    {
        $data = Payment::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Payment::class, $result);
        $this->assertDatabaseHas('payments', ['id' => $result->id]);
    }

    public function test_can_update_payment(): void
    {
        $payment = Payment::factory()->create();
        $updateData = Payment::factory()->make()->toArray();

        $result = $this->service->update($payment->id, $updateData);

        $this->assertInstanceOf(Payment::class, $result);
    }

    public function test_can_delete_payment(): void
    {
        $payment = Payment::factory()->create();

        $result = $this->service->delete($payment->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('payments', ['id' => $payment->id]);
    }

    public function test_can_find_payment_by_id(): void
    {
        $payment = Payment::factory()->create();

        $result = $this->service->findById($payment->id);

        $this->assertInstanceOf(Payment::class, $result);
        $this->assertEquals($payment->id, $result->id);
    }
}
