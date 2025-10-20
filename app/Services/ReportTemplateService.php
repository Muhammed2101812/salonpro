<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ReportTemplateRepositoryInterface;

class ReportTemplateService extends BaseService
{
    public function __construct(ReportTemplateRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
