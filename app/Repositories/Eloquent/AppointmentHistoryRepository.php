<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\AppointmentHistory;
use App\Repositories\Contracts\AppointmentHistoryRepositoryInterface;

class AppointmentHistoryRepository extends BaseRepository implements AppointmentHistoryRepositoryInterface
{
    public function __construct(AppointmentHistory $model)
    {
        parent::__construct($model);
    }

    public function findByAppointment(string $appointmentId)
    {
        return $this->model->where('appointment_id', $appointmentId)
            ->with(['appointment', 'performedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getRecentChanges(int $limit = 50)
    {
        return $this->model->with(['appointment', 'performedBy'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
