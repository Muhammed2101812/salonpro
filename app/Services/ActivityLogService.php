<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ActivityLogRepositoryInterface;

class ActivityLogService extends BaseService
{
    public function __construct(ActivityLogRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
