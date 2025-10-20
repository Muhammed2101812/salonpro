<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Review;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use App\Services\ServiceReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceReviewServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceReviewService $service;
    protected ReviewRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ReviewRepositoryInterface::class);
        $this->service = new ServiceReviewService($this->repository);
    }

    public function test_can_get_all_reviews(): void
    {
        Review::factory()->count(5)->create();

        $result = $this->service->getAll();

        $this->assertCount(5, $result);
    }

    public function test_can_get_paginated_reviews(): void
    {
        Review::factory()->count(20)->create();

        $result = $this->service->getPaginated(10);

        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(20, $result->total());
    }

    public function test_can_create_review(): void
    {
        $data = Review::factory()->make()->toArray();

        $result = $this->service->create($data);

        $this->assertInstanceOf(Review::class, $result);
        $this->assertDatabaseHas('reviews', ['id' => $result->id]);
    }

    public function test_can_update_review(): void
    {
        $review = Review::factory()->create();
        $updateData = Review::factory()->make()->toArray();

        $result = $this->service->update($review->id, $updateData);

        $this->assertInstanceOf(Review::class, $result);
    }

    public function test_can_delete_review(): void
    {
        $review = Review::factory()->create();

        $result = $this->service->delete($review->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('reviews', ['id' => $review->id]);
    }

    public function test_can_find_review_by_id(): void
    {
        $review = Review::factory()->create();

        $result = $this->service->findById($review->id);

        $this->assertInstanceOf(Review::class, $result);
        $this->assertEquals($review->id, $result->id);
    }
}
