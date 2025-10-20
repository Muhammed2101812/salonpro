<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\LeadRepositoryInterface;

class LeadService extends BaseService
{
    public function __construct(LeadRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
