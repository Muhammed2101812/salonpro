<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ProductBarcodeRepositoryInterface;

class ProductBarcodeService extends BaseService
{
    public function __construct(ProductBarcodeRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
