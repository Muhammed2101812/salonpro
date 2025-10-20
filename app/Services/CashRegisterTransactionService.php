<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CashRegisterTransactionRepositoryInterface;

class CashRegisterTransactionService extends BaseService
{
    public function __construct(CashRegisterTransactionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
