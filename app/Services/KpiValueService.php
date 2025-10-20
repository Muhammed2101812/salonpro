<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\KpiValueRepositoryInterface;

class KpiValueService extends BaseService
{
    public function __construct(KpiValueRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
