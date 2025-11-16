<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\NotificationQueue;
use App\Repositories\Contracts\NotificationQueueRepositoryInterface;

class NotificationQueueRepository extends BaseRepository implements NotificationQueueRepositoryInterface
{
    public function __construct(NotificationQueue $model)
    {
        parent::__construct($model);
    }

    public function getPendingNotifications(int $limit = 100)
    {
        return $this->model->where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('scheduled_at')
                    ->orWhere('scheduled_at', '<=', now());
            })
            ->orderBy('created_at')
            ->limit($limit)
            ->get();
    }

    public function getFailedNotifications(int $maxAttempts = 3)
    {
        return $this->model->where('status', 'failed')
            ->where('attempts', '<', $maxAttempts)
            ->orderBy('created_at')
            ->get();
    }

    public function markAsSent(string $id)
    {
        return $this->update($id, [
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed(string $id, string $errorMessage)
    {
        $notification = $this->find($id);
        
        return $this->update($id, [
            'status' => 'failed',
            'attempts' => $notification->attempts + 1,
            'error_message' => $errorMessage,
        ]);
    }
}
