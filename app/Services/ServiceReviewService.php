<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ServiceReviewRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Services\Contracts\ServiceReviewServiceInterface;
use Illuminate\Support\Facades\DB;

class ServiceReviewService implements ServiceReviewServiceInterface
{
    public function __construct(
        private ServiceReviewRepositoryInterface $serviceReviewRepository,
        private ServiceRepositoryInterface $serviceRepository
    ) {}

    public function createReview(array $data)
    {
        $service = $this->serviceRepository->findOrFail($data['service_id']);

        return DB::transaction(function () use ($data) {
            $reviewData = [
                'service_id' => $data['service_id'],
                'customer_id' => $data['customer_id'],
                'appointment_id' => $data['appointment_id'] ?? null,
                'rating' => $data['rating'],
                'review_text' => $data['review_text'] ?? null,
                'is_published' => $data['is_published'] ?? false,
                'reviewed_at' => now(),
            ];

            $review = $this->serviceReviewRepository->create($reviewData);

            // Update service average rating
            $this->updateServiceRating($data['service_id']);

            return $review;
        });
    }

    public function updateReview(string $id, array $data)
    {
        $review = $this->serviceReviewRepository->findOrFail($id);

        return DB::transaction(function () use ($id, $review, $data) {
            $updated = $this->serviceReviewRepository->update($id, $data);

            // Update service average rating if rating changed
            if (isset($data['rating'])) {
                $this->updateServiceRating($review->service_id);
            }

            return $updated;
        });
    }

    public function deleteReview(string $id)
    {
        $review = $this->serviceReviewRepository->findOrFail($id);
        $serviceId = $review->service_id;

        return DB::transaction(function () use ($id, $serviceId) {
            $this->serviceReviewRepository->delete($id);

            // Update service average rating
            $this->updateServiceRating($serviceId);

            return true;
        });
    }

    public function approveReview(string $id)
    {
        return $this->serviceReviewRepository->update($id, [
            'is_published' => true,
            'approved_at' => now(),
        ]);
    }

    public function rejectReview(string $id, string $reason)
    {
        return $this->serviceReviewRepository->update($id, [
            'is_published' => false,
            'rejection_reason' => $reason,
            'rejected_at' => now(),
        ]);
    }

    public function getServiceReviews(string $serviceId, int $perPage = 15)
    {
        return $this->serviceReviewRepository->findByService($serviceId)
            ->paginate($perPage);
    }

    public function getServiceAverageRating(string $serviceId): float
    {
        return $this->serviceReviewRepository->getAverageRating($serviceId);
    }

    public function getPublishedReviews(string $serviceId, int $perPage = 15)
    {
        return $this->serviceReviewRepository->getPublishedReviews($serviceId, $perPage);
    }

    private function updateServiceRating(string $serviceId): void
    {
        $averageRating = $this->getServiceAverageRating($serviceId);
        $totalReviews = $this->serviceReviewRepository->findByService($serviceId)->count();

        $this->serviceRepository->update($serviceId, [
            'average_rating' => $averageRating,
            'total_reviews' => $totalReviews,
        ]);
    }
}
