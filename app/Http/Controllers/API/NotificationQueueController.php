<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreNotificationQueueRequest;
use App\Http\Requests\UpdateNotificationQueueRequest;
use App\Http\Resources\NotificationQueueResource;
use App\Services\NotificationQueueService;
use App\Models\NotificationQueue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationQueueController extends BaseController
{
    public function __construct(
        protected NotificationQueueService $notificationQueueService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', NotificationQueue::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $notificationQueues = $this->notificationQueueService->getPaginated($perPage);

            return $this->sendPaginated(
                NotificationQueueResource::collection($notificationQueues),
                'NotificationQueues başarıyla getirildi'
            );
        }

        $notificationQueues = $this->notificationQueueService->getAll();

        return NotificationQueueResource::collection($notificationQueues);
    }

    public function store(StoreNotificationQueueRequest $request): JsonResponse
    {
        $this->authorize('create', NotificationQueue::class);

        $notificationQueue = $this->notificationQueueService->create($request->validated());

        return $this->sendSuccess(
            new NotificationQueueResource($notificationQueue),
            'NotificationQueue başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $notificationQueue = $this->notificationQueueService->findByIdOrFail($id);

        return $this->sendSuccess(
            new NotificationQueueResource($notificationQueue),
            'NotificationQueue başarıyla getirildi'
        );
    }

    public function update(UpdateNotificationQueueRequest $request, string $id): JsonResponse
    {
        $notificationQueue = $this->notificationQueueService->update($id, $request->validated());

        return $this->sendSuccess(
            new NotificationQueueResource($notificationQueue),
            'NotificationQueue başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->notificationQueueService->delete($id);

        return $this->sendSuccess(
            null,
            'NotificationQueue başarıyla silindi'
        );
    }
}
