<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ChartOfAccountRepositoryInterface;

class ChartOfAccountService extends BaseService
{
    public function __construct(ChartOfAccountRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
