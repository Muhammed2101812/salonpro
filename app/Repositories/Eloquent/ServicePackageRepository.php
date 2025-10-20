<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ServicePackage;
use App\Repositories\Contracts\ServicePackageRepositoryInterface;

class ServicePackageRepository extends BaseRepository implements ServicePackageRepositoryInterface
{
    public function __construct(ServicePackage $model)
    {
        parent::__construct($model);
    }
}
