<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ProductSupplierPriceRepositoryInterface;

class ProductSupplierPriceService extends BaseService
{
    public function __construct(ProductSupplierPriceRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
