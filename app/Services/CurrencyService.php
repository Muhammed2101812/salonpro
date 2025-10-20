<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CurrencyRepositoryInterface;

class CurrencyService extends BaseService
{
    public function __construct(CurrencyRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
