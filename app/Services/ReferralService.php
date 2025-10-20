<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ReferralRepositoryInterface;

class ReferralService extends BaseService
{
    public function __construct(ReferralRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
