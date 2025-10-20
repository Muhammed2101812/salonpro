<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ProductVariantRepositoryInterface;

class ProductVariantService extends BaseService
{
    public function __construct(ProductVariantRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
