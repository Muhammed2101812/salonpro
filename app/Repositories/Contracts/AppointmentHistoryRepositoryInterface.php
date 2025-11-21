<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface AppointmentHistoryRepositoryInterface extends BaseRepositoryInterface
{
    public function findByAppointment(string $appointmentId);
    public function getRecentChanges(int $limit = 50);
}
