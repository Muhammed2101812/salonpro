<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\AnalyticsEventRepositoryInterface;

class AnalyticsEventService extends BaseService
{
    public function __construct(AnalyticsEventRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
