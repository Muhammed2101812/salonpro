<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\FeatureFlagRepositoryInterface;

class FeatureFlagService extends BaseService
{
    public function __construct(FeatureFlagRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
