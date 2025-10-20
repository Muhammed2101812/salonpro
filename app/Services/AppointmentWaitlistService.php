<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\AppointmentWaitlistRepositoryInterface;

class AppointmentWaitlistService extends BaseService
{
    public function __construct(AppointmentWaitlistRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
