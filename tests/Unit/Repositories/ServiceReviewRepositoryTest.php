<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\ServiceReview;
use App\Repositories\Eloquent\ServiceReviewRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceReviewRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceReviewRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ServiceReviewRepository(new ServiceReview());
    }

    public function test_can_get_all_records(): void
    {
        ServiceReview::factory()->count(3)->create();

        $result = $this->repository->all();

        $this->assertCount(3, $result);
    }

    public function test_can_create_record(): void
    {
        $data = ServiceReview::factory()->make()->toArray();

        $result = $this->repository->create($data);

        $this->assertInstanceOf(ServiceReview::class, $result);
        $this->assertDatabaseHas('serviceReviews', ['id' => $result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        $serviceReview = ServiceReview::factory()->create();

        $result = $this->repository->find($serviceReview->id);

        $this->assertInstanceOf(ServiceReview::class, $result);
        $this->assertEquals($serviceReview->id, $result->id);
    }

    public function test_can_update_record(): void
    {
        $serviceReview = ServiceReview::factory()->create();
        $updateData = ServiceReview::factory()->make()->toArray();

        $result = $this->repository->update($serviceReview->id, $updateData);

        $this->assertInstanceOf(ServiceReview::class, $result);
    }

    public function test_can_delete_record(): void
    {
        $serviceReview = ServiceReview::factory()->create();

        $result = $this->repository->delete($serviceReview->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('serviceReviews', ['id' => $serviceReview->id]);
    }
}
