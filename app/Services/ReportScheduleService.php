<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ReportScheduleRepositoryInterface;

class ReportScheduleService extends BaseService
{
    public function __construct(ReportScheduleRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
