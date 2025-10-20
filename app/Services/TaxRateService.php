<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\TaxRateRepositoryInterface;

class TaxRateService extends BaseService
{
    public function __construct(TaxRateRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
