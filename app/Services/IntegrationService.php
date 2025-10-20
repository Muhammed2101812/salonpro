<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\IntegrationRepositoryInterface;

class IntegrationService extends BaseService
{
    public function __construct(IntegrationRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
