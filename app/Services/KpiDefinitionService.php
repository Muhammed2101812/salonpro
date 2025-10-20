<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\KpiDefinitionRepositoryInterface;

class KpiDefinitionService extends BaseService
{
    public function __construct(KpiDefinitionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
