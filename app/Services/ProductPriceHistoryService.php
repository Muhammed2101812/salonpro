<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ProductPriceHistoryRepositoryInterface;

class ProductPriceHistoryService extends BaseService
{
    public function __construct(ProductPriceHistoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
