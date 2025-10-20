<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ProductAttributeValueRepositoryInterface;

class ProductAttributeValueService extends BaseService
{
    public function __construct(ProductAttributeValueRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
