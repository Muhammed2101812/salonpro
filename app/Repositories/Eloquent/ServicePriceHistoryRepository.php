<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ServicePriceHistory;
use App\Repositories\Contracts\ServicePriceHistoryRepositoryInterface;

class ServicePriceHistoryRepository extends BaseRepository implements ServicePriceHistoryRepositoryInterface
{
    public function __construct(ServicePriceHistory $model)
    {
        parent::__construct($model);
    }
}
