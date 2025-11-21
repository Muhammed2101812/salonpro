<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\NotificationQueueRepositoryInterface;
use App\Repositories\Contracts\NotificationTemplateRepositoryInterface;
use App\Services\Contracts\NotificationServiceInterface;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService implements NotificationServiceInterface
{
    public function __construct(
        private NotificationQueueRepositoryInterface $notificationQueueRepository,
        private NotificationTemplateRepositoryInterface $notificationTemplateRepository
    ) {}

    public function queueNotification(array $data)
    {
        $notificationData = [
            'recipient_type' => $data['recipient_type'],
            'recipient_id' => $data['recipient_id'],
            'channel' => $data['channel'], // email, sms, push
            'template_id' => $data['template_id'] ?? null,
            'subject' => $data['subject'] ?? null,
            'content' => $data['content'],
            'data' => $data['data'] ?? null,
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'status' => 'pending',
            'priority' => $data['priority'] ?? 'normal',
        ];

        return $this->notificationQueueRepository->create($notificationData);
    }

    public function sendNotification(string $id)
    {
        $notification = $this->notificationQueueRepository->findOrFail($id);

        try {
            switch ($notification->channel) {
                case 'email':
                    $this->sendEmail($notification);
                    break;
                case 'sms':
                    $this->sendSms($notification);
                    break;
                case 'push':
                    $this->sendPushNotification($notification);
                    break;
                default:
                    throw new \Exception("Unsupported notification channel: {$notification->channel}");
            }

            $this->markAsSent($id);

            return true;
        } catch (\Exception $e) {
            $this->markAsFailed($id, $e->getMessage());
            Log::error("Notification {$id} failed: " . $e->getMessage());

            return false;
        }
    }

    public function processPendingNotifications(int $limit = 100)
    {
        $notifications = $this->notificationQueueRepository->getPendingNotifications($limit);

        $sent = 0;
        $failed = 0;

        foreach ($notifications as $notification) {
            if ($this->sendNotification($notification->id)) {
                $sent++;
            } else {
                $failed++;
            }
        }

        return [
            'sent' => $sent,
            'failed' => $failed,
            'total' => $notifications->count(),
        ];
    }

    public function getPendingCount(): int
    {
        return $this->notificationQueueRepository->getPendingNotifications(1000)->count();
    }

    public function getFailedNotifications(int $perPage = 15)
    {
        return $this->notificationQueueRepository->getFailedNotifications($perPage);
    }

    public function retryFailedNotification(string $id)
    {
        $notification = $this->notificationQueueRepository->findOrFail($id);

        if ($notification->status !== 'failed') {
            throw new \Exception('Only failed notifications can be retried');
        }

        // Reset status to pending
        $this->notificationQueueRepository->update($id, [
            'status' => 'pending',
            'error_message' => null,
        ]);

        return $this->sendNotification($id);
    }

    public function markAsSent(string $id)
    {
        return $this->notificationQueueRepository->markAsSent($id);
    }

    public function markAsFailed(string $id, string $error)
    {
        return $this->notificationQueueRepository->markAsFailed($id, $error);
    }

    private function sendEmail($notification)
    {
        // TODO: Implement email sending logic
        // Mail::to($notification->recipient->email)->send(new GenericNotification($notification));

        Log::info("Email sent to notification {$notification->id}");
    }

    private function sendSms($notification)
    {
        // TODO: Implement SMS sending logic using a service like Twilio

        Log::info("SMS sent for notification {$notification->id}");
    }

    private function sendPushNotification($notification)
    {
        // TODO: Implement push notification logic using FCM or similar

        Log::info("Push notification sent for {$notification->id}");
    }
}
