<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CustomerSegmentRepositoryInterface;

class CustomerSegmentService extends BaseService
{
    public function __construct(CustomerSegmentRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
