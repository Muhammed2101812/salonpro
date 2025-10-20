<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\BudgetItemRepositoryInterface;

class BudgetItemService extends BaseService
{
    public function __construct(BudgetItemRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
