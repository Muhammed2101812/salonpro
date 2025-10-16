<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ServiceCategoryRepositoryInterface;

class ServiceCategoryService extends BaseService
{
    public function __construct(ServiceCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
