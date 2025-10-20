<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CashRegisterRepositoryInterface;

class CashRegisterService extends BaseService
{
    public function __construct(CashRegisterRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
