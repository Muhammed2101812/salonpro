<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\AppointmentHistoryRepositoryInterface;

class AppointmentHistoryService extends BaseService
{
    public function __construct(AppointmentHistoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
