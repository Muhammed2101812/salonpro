<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ServiceRequirement;
use App\Repositories\Contracts\ServiceRequirementRepositoryInterface;

class ServiceRequirementRepository extends BaseRepository implements ServiceRequirementRepositoryInterface
{
    public function __construct(ServiceRequirement $model)
    {
        parent::__construct($model);
    }
}
