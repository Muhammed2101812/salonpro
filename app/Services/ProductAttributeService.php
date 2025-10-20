<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ProductAttributeRepositoryInterface;

class ProductAttributeService extends BaseService
{
    public function __construct(ProductAttributeRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
