<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\AppointmentGroupRepositoryInterface;

class AppointmentGroupService extends BaseService
{
    public function __construct(AppointmentGroupRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
