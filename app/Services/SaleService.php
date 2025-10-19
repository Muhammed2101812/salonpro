<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\SaleRepositoryInterface;

class SaleService extends BaseService
{
    public function __construct(SaleRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
