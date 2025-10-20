<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\BudgetPlanRepositoryInterface;

class BudgetPlanService extends BaseService
{
    public function __construct(BudgetPlanRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
