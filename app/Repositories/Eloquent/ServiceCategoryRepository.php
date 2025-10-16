<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ServiceCategory;
use App\Repositories\Contracts\ServiceCategoryRepositoryInterface;

class ServiceCategoryRepository extends BaseRepository implements ServiceCategoryRepositoryInterface
{
    public function __construct(ServiceCategory $model)
    {
        parent::__construct($model);
    }
}
