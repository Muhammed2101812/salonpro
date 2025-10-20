<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\PerformanceMetricRepositoryInterface;

class PerformanceMetricService extends BaseService
{
    public function __construct(PerformanceMetricRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
