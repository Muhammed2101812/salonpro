<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface AppointmentCancellationRepositoryInterface extends BaseRepositoryInterface
{
    public function findByAppointment(string $appointmentId);
    public function getStatsByPeriod(string $startDate, string $endDate, ?string $branchId = null);
    public function getTopCancellationReasons(int $limit = 10);
}
