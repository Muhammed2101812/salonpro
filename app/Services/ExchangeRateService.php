<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ExchangeRateRepositoryInterface;

class ExchangeRateService extends BaseService
{
    public function __construct(ExchangeRateRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
