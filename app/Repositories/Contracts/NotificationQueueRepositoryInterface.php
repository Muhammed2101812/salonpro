<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface NotificationQueueRepositoryInterface extends BaseRepositoryInterface
{
    public function getPendingNotifications(int $limit = 100);
    public function getFailedNotifications(int $maxAttempts = 3);
    public function markAsSent(string $id);
    public function markAsFailed(string $id, string $errorMessage);
}
