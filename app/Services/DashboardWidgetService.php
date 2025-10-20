<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\DashboardWidgetRepositoryInterface;

class DashboardWidgetService extends BaseService
{
    public function __construct(DashboardWidgetRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
