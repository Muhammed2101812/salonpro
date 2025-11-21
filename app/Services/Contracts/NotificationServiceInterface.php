<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface NotificationServiceInterface
{
    public function queueNotification(array $data);
    public function sendNotification(string $id);
    public function processPendingNotifications(int $limit = 100);
    public function getPendingCount(): int;
    public function getFailedNotifications(int $perPage = 15);
    public function retryFailedNotification(string $id);
    public function markAsSent(string $id);
    public function markAsFailed(string $id, string $error);
}
