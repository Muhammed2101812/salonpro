<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ServiceReview;
use App\Repositories\Contracts\ServiceReviewRepositoryInterface;

class ServiceReviewRepository extends BaseRepository implements ServiceReviewRepositoryInterface
{
    public function __construct(ServiceReview $model)
    {
        parent::__construct($model);
    }

    public function findByService(string $serviceId, int $perPage = 15)
    {
        return $this->model->where('service_id', $serviceId)
            ->with(['customer', 'appointment'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getAverageRating(string $serviceId)
    {
        return $this->model->where('service_id', $serviceId)
            ->where('is_published', true)
            ->avg('rating');
    }

    public function getPublishedReviews(string $serviceId, int $perPage = 10)
    {
        return $this->model->where('service_id', $serviceId)
            ->where('is_published', true)
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
