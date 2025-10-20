<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ServiceTemplate;
use App\Repositories\Contracts\ServiceTemplateRepositoryInterface;

class ServiceTemplateRepository extends BaseRepository implements ServiceTemplateRepositoryInterface
{
    public function __construct(ServiceTemplate $model)
    {
        parent::__construct($model);
    }
}
