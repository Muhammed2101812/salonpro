<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\AppointmentGroupParticipantRepositoryInterface;

class AppointmentGroupParticipantService extends BaseService
{
    public function __construct(AppointmentGroupParticipantRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
