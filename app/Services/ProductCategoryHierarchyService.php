<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ProductCategoryHierarchyRepositoryInterface;

class ProductCategoryHierarchyService extends BaseService
{
    public function __construct(ProductCategoryHierarchyRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
