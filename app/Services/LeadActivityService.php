<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\LeadActivityRepositoryInterface;

class LeadActivityService extends BaseService
{
    public function __construct(LeadActivityRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
