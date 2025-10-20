<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ProductBundleItemRepositoryInterface;

class ProductBundleItemService extends BaseService
{
    public function __construct(ProductBundleItemRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
