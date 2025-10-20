<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CashRegisterSessionRepositoryInterface;

class CashRegisterSessionService extends BaseService
{
    public function __construct(CashRegisterSessionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
