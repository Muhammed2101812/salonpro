<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Customer;
use App\Repositories\Eloquent\CustomerRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CustomerRepository(new Customer());
    }

    public function test_can_get_all_records(): void
    {
        Customer::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = Customer::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(Customer::class, $result);
        $this->assertDatabaseHas('customers', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $customer = Customer::factory()->create();

        $result = $this->repository->find($customer->id);

        $this->assertInstanceOf(Customer::class, $result);
        $this->assertEquals($customer->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $customer = Customer::factory()->create();
        $updateData = Customer::factory()->make()->toArray();

        $result = $this->repository->update($customer->id, $updateData);

        $this->assertInstanceOf(Customer::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $customer = Customer::factory()->create();

        $result = $this->repository->delete($customer->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }
}
