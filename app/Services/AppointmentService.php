<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\AppointmentRepositoryInterface;

class AppointmentService extends BaseService
{
    public function __construct(AppointmentRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
