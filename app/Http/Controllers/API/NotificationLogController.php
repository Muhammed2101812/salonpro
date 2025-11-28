<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreNotificationLogRequest;
use App\Http\Requests\UpdateNotificationLogRequest;
use App\Http\Resources\NotificationLogResource;
use App\Services\NotificationLogService;
use App\Models\NotificationLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationLogController extends BaseController
{
    public function __construct(
        protected NotificationLogService $notificationLogService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', NotificationLog::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $notificationLogs = $this->notificationLogService->getPaginated($perPage);

            return $this->sendPaginated(
                NotificationLogResource::collection($notificationLogs),
                'NotificationLogs başarıyla getirildi'
            );
        }

        $notificationLogs = $this->notificationLogService->getAll();

        return NotificationLogResource::collection($notificationLogs);
    }

    public function store(StoreNotificationLogRequest $request): JsonResponse
    {
        $this->authorize('create', NotificationLog::class);

        $notificationLog = $this->notificationLogService->create($request->validated());

        return $this->sendSuccess(
            new NotificationLogResource($notificationLog),
            'NotificationLog başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $notificationLog = $this->notificationLogService->findByIdOrFail($id);

        return $this->sendSuccess(
            new NotificationLogResource($notificationLog),
            'NotificationLog başarıyla getirildi'
        );
    }

    public function update(UpdateNotificationLogRequest $request, string $id): JsonResponse
    {
        $notificationLog = $this->notificationLogService->update($id, $request->validated());

        return $this->sendSuccess(
            new NotificationLogResource($notificationLog),
            'NotificationLog başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->notificationLogService->delete($id);

        return $this->sendSuccess(
            null,
            'NotificationLog başarıyla silindi'
        );
    }
}
