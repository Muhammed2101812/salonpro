<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\LoyaltyProgramRepositoryInterface;

class LoyaltyProgramService extends BaseService
{
    public function __construct(LoyaltyProgramRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
