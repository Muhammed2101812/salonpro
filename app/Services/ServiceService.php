<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ServiceRepositoryInterface;

class ServiceService extends BaseService
{
    public function __construct(ServiceRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
