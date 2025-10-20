<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\AppointmentCancellationReasonRepositoryInterface;

class AppointmentCancellationReasonService extends BaseService
{
    public function __construct(AppointmentCancellationReasonRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
