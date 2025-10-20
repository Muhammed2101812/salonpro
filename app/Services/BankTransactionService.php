<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\BankTransactionRepositoryInterface;

class BankTransactionService extends BaseService
{
    public function __construct(BankTransactionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
