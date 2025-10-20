<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\LoyaltyPointTransactionRepositoryInterface;

class LoyaltyPointTransactionService extends BaseService
{
    public function __construct(LoyaltyPointTransactionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
