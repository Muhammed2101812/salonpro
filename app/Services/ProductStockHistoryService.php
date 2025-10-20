<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ProductStockHistoryRepositoryInterface;

class ProductStockHistoryService extends BaseService
{
    public function __construct(ProductStockHistoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
