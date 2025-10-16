<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\BranchRepositoryInterface;

class BranchService extends BaseService
{
    public function __construct(BranchRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
