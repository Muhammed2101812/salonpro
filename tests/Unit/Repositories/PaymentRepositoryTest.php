<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Payment;
use App\Repositories\Eloquent\PaymentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PaymentRepository(new Payment());
    }

    public function test_can_get_all_records(): void
    {
        Payment::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = Payment::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(Payment::class, $result);
        $this->assertDatabaseHas('payments', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $payment = Payment::factory()->create();

        $result = $this->repository->find($payment->id);

        $this->assertInstanceOf(Payment::class, $result);
        $this->assertEquals($payment->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $payment = Payment::factory()->create();
        $updateData = Payment::factory()->make()->toArray();

        $result = $this->repository->update($payment->id, $updateData);

        $this->assertInstanceOf(Payment::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $payment = Payment::factory()->create();

        $result = $this->repository->delete($payment->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('payments', ['id' => $payment->id]);
    }
}
