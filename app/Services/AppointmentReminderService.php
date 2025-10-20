<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\AppointmentReminderRepositoryInterface;

class AppointmentReminderService extends BaseService
{
    public function __construct(AppointmentReminderRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
