<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentRepositoryInterface;

class AppointmentRepository extends BaseRepository implements AppointmentRepositoryInterface
{
    public function __construct(Appointment $model)
    {
        parent::__construct($model);
    }
}
