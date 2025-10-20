<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\BankAccountRepositoryInterface;

class BankAccountService extends BaseService
{
    public function __construct(BankAccountRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
