<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Contracts\NotificationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationServiceInterface $notificationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\NotificationQueue::class);

        $status = $request->input('status');

        if ($status === 'failed') {
            $notifications = $this->notificationService->getFailedNotifications(
                $request->input('per_page', 15)
            );
        } else {
            $query = \App\Models\NotificationQueue::with(['recipient']);

            if ($status) {
                $query->where('status', $status);
            }

            $notifications = $query->paginate($request->input('per_page', 15));
        }

        return response()->json($notifications);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', \App\Models\NotificationQueue::class);

        $validated = $request->validate([
            'recipient_type' => 'required|string',
            'recipient_id' => 'required|uuid',
            'channel' => 'required|in:email,sms,push',
            'template_id' => 'nullable|uuid|exists:notification_templates,id',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'data' => 'nullable|json',
            'scheduled_at' => 'nullable|date',
            'priority' => 'nullable|in:low,normal,high',
        ]);

        $notification = $this->notificationService->queueNotification($validated);

        return response()->json($notification, 201);
    }

    public function show(string $id): JsonResponse
    {
        $notification = \App\Models\NotificationQueue::findOrFail($id);
        $this->authorize('view', $notification);

        return response()->json($notification);
    }

    public function send(string $id): JsonResponse
    {
        $notification = \App\Models\NotificationQueue::findOrFail($id);
        $this->authorize('update', $notification);

        $sent = $this->notificationService->sendNotification($id);

        if ($sent) {
            return response()->json(['message' => 'Notification sent successfully']);
        }

        return response()->json(['message' => 'Failed to send notification'], 500);
    }

    public function processPending(Request $request): JsonResponse
    {
        $this->authorize('create', \App\Models\NotificationQueue::class);

        $limit = $request->input('limit', 100);
        $result = $this->notificationService->processPendingNotifications($limit);

        return response()->json($result);
    }

    public function pendingCount(): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\NotificationQueue::class);

        $count = $this->notificationService->getPendingCount();

        return response()->json(['count' => $count]);
    }

    public function retry(string $id): JsonResponse
    {
        $notification = \App\Models\NotificationQueue::findOrFail($id);
        $this->authorize('update', $notification);

        try {
            $sent = $this->notificationService->retryFailedNotification($id);

            if ($sent) {
                return response()->json(['message' => 'Notification resent successfully']);
            }

            return response()->json(['message' => 'Failed to resend notification'], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
