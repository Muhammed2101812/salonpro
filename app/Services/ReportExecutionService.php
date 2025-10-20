<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ReportExecutionRepositoryInterface;

class ReportExecutionService extends BaseService
{
    public function __construct(ReportExecutionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
