<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ServiceReviewRepositoryInterface;

class ServiceReviewService extends BaseService
{
    public function __construct(ServiceReviewRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Get reviews for a service
     */
    public function getServiceReviews(string $serviceId, bool $publishedOnly = true): mixed
    {
        $query = $this->repository->query()
            ->where('service_id', $serviceId)
            ->with(['service', 'customer', 'appointment']);
        
        if ($publishedOnly) {
            $query->where('is_published', true);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get average rating for a service
     */
    public function getAverageRating(string $serviceId): float
    {
        return (float) $this->repository->query()
            ->where('service_id', $serviceId)
            ->where('is_published', true)
            ->avg('rating') ?? 0;
    }

    /**
     * Get rating distribution for a service
     */
    public function getRatingDistribution(string $serviceId): array
    {
        $reviews = $this->repository->query()
            ->where('service_id', $serviceId)
            ->where('is_published', true)
            ->get();

        $distribution = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];

        foreach ($reviews as $review) {
            $distribution[$review->rating]++;
        }

        return $distribution;
    }

    /**
     * Get reviews by customer
     */
    public function getCustomerReviews(string $customerId): mixed
    {
        return $this->repository->query()
            ->where('customer_id', $customerId)
            ->with(['service', 'appointment'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get reviews by rating
     */
    public function getReviewsByRating(int $rating, bool $publishedOnly = true): mixed
    {
        $query = $this->repository->query()
            ->where('rating', $rating)
            ->with(['service', 'customer', 'appointment']);
        
        if ($publishedOnly) {
            $query->where('is_published', true);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get positive reviews (4-5 stars)
     */
    public function getPositiveReviews(bool $publishedOnly = true): mixed
    {
        $query = $this->repository->query()
            ->where('rating', '>=', 4)
            ->with(['service', 'customer', 'appointment']);
        
        if ($publishedOnly) {
            $query->where('is_published', true);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get negative reviews (1-2 stars)
     */
    public function getNegativeReviews(bool $publishedOnly = true): mixed
    {
        $query = $this->repository->query()
            ->where('rating', '<=', 2)
            ->with(['service', 'customer', 'appointment']);
        
        if ($publishedOnly) {
            $query->where('is_published', true);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Publish a review
     */
    public function publishReview(string $id): mixed
    {
        return $this->update($id, ['is_published' => true]);
    }

    /**
     * Unpublish a review
     */
    public function unpublishReview(string $id): mixed
    {
        return $this->update($id, ['is_published' => false]);
    }
}
