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
}
