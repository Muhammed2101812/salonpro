<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\AppointmentRecurrenceRepositoryInterface;

class AppointmentRecurrenceService extends BaseService
{
    public function __construct(AppointmentRecurrenceRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
