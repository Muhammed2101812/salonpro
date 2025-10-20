<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ProductDiscountRepositoryInterface;

class ProductDiscountService extends BaseService
{
    public function __construct(ProductDiscountRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
