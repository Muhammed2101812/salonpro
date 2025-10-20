<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\AppointmentCancellationRepositoryInterface;

class AppointmentCancellationService extends BaseService
{
    public function __construct(AppointmentCancellationRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
