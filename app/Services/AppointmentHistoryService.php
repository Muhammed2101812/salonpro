<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\AppointmentHistoryRepositoryInterface;
use App\Services\Contracts\AppointmentHistoryServiceInterface;

class AppointmentHistoryService implements AppointmentHistoryServiceInterface
{
    public function __construct(
        private AppointmentHistoryRepositoryInterface $appointmentHistoryRepository
    ) {}

    public function logChange(string $appointmentId, string $action, array $changes, ?string $userId = null)
    {
        $historyData = [
            'appointment_id' => $appointmentId,
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'old_values' => $changes['old'] ?? null,
            'new_values' => $changes['new'] ?? null,
            'changed_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        return $this->appointmentHistoryRepository->create($historyData);
    }

    public function getAppointmentHistory(string $appointmentId)
    {
        return $this->appointmentHistoryRepository->findByAppointment($appointmentId);
    }

    public function getRecentChanges(int $limit = 50)
    {
        return $this->appointmentHistoryRepository->getRecentChanges($limit);
    }

    public function getChangesByUser(string $userId, int $perPage = 15)
    {
        return $this->appointmentHistoryRepository
            ->query()
            ->where('user_id', $userId)
            ->with(['appointment', 'user'])
            ->orderBy('changed_at', 'desc')
            ->paginate($perPage);
    }
}
