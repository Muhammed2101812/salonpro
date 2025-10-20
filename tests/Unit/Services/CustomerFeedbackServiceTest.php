<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\CustomerFeedback;
use App\Repositories\Contracts\CustomerFeedbackRepositoryInterface;
use App\Services\CustomerFeedbackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerFeedbackServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerFeedbackService $service;
    protected CustomerFeedbackRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CustomerFeedbackRepositoryInterface::class);
        $this->service = new CustomerFeedbackService($this->repository);
    }

    public function test_can_get_all_customerFeedbacks(): void
    {
        CustomerFeedback::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_customerFeedbacks(): void
    {
        CustomerFeedback::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_customerFeedback(): void
    {
        $data = CustomerFeedback::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(CustomerFeedback::class, $result);
        $this->assertDatabaseHas('customerFeedbacks', ['id' => $result->id]);
    }

    public function test_can_update_customerFeedback(): void
    {
        $customerFeedback = CustomerFeedback::factory()->create();
        $updateData = CustomerFeedback::factory()->make()->toArray();

        $result = $this->service->update($customerFeedback->id, $updateData);

        $this->assertInstanceOf(CustomerFeedback::class, $result);
    }

    public function test_can_delete_customerFeedback(): void
    {
        $customerFeedback = CustomerFeedback::factory()->create();

        $result = $this->service->delete($customerFeedback->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('customerFeedbacks', ['id' => $customerFeedback->id]);
    }

    public function test_can_find_customerFeedback_by_id(): void
    {
        $customerFeedback = CustomerFeedback::factory()->create();

        $result = $this->service->findById($customerFeedback->id);

        $this->assertInstanceOf(CustomerFeedback::class, $result);
        $this->assertEquals($customerFeedback->id, $result->id);
    }
}
