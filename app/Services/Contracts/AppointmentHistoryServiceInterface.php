<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface AppointmentHistoryServiceInterface
{
    public function logChange(string $appointmentId, string $action, array $changes, ?string $userId = null);
    public function getAppointmentHistory(string $appointmentId);
    public function getRecentChanges(int $limit = 50);
    public function getChangesByUser(string $userId, int $perPage = 15);
}
