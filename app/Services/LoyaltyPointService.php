<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\LoyaltyPointRepositoryInterface;

class LoyaltyPointService extends BaseService
{
    public function __construct(LoyaltyPointRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
