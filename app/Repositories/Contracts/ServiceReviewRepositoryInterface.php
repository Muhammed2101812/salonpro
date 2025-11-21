<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface ServiceReviewRepositoryInterface extends BaseRepositoryInterface
{
    public function findByService(string $serviceId, int $perPage = 15);
    public function getAverageRating(string $serviceId);
    public function getPublishedReviews(string $serviceId, int $perPage = 10);
}
