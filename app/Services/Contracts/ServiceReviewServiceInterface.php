<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface ServiceReviewServiceInterface
{
    public function createReview(array $data);
    public function updateReview(string $id, array $data);
    public function deleteReview(string $id);
    public function approveReview(string $id);
    public function rejectReview(string $id, string $reason);
    public function getServiceReviews(string $serviceId, int $perPage = 15);
    public function getServiceAverageRating(string $serviceId): float;
    public function getPublishedReviews(string $serviceId, int $perPage = 15);
}
