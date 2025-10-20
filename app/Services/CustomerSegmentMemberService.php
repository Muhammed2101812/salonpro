<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CustomerSegmentMemberRepositoryInterface;

class CustomerSegmentMemberService extends BaseService
{
    public function __construct(CustomerSegmentMemberRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
