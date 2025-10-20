<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Services\CustomerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerService $service;
    protected CustomerRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CustomerRepositoryInterface::class);
        $this->service = new CustomerService($this->repository);
    }

    public function test_can_get_all_customers(): void
    {
        Customer::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_customers(): void
    {
        Customer::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_customer(): void
    {
        $data = Customer::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Customer::class, $result);
        $this->assertDatabaseHas('customers', ['id' => $result->id]);
    }

    public function test_can_update_customer(): void
    {
        $customer = Customer::factory()->create();
        $updateData = Customer::factory()->make()->toArray();

        $result = $this->service->update($customer->id, $updateData);

        $this->assertInstanceOf(Customer::class, $result);
    }

    public function test_can_delete_customer(): void
    {
        $customer = Customer::factory()->create();

        $result = $this->service->delete($customer->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }

    public function test_can_find_customer_by_id(): void
    {
        $customer = Customer::factory()->create();

        $result = $this->service->findById($customer->id);

        $this->assertInstanceOf(Customer::class, $result);
        $this->assertEquals($customer->id, $result->id);
    }
}
