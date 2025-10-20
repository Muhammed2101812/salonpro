<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ServiceAddon;
use App\Repositories\Contracts\ServiceAddonRepositoryInterface;

class ServiceAddonRepository extends BaseRepository implements ServiceAddonRepositoryInterface
{
    public function __construct(ServiceAddon $model)
    {
        parent::__construct($model);
    }
}
