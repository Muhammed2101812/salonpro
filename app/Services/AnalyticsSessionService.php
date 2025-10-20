<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\AnalyticsSessionRepositoryInterface;

class AnalyticsSessionService extends BaseService
{
    public function __construct(AnalyticsSessionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
