<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ProductImageRepositoryInterface;

class ProductImageService extends BaseService
{
    public function __construct(ProductImageRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
