<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreNotificationPreferenceRequest;
use App\Http\Requests\UpdateNotificationPreferenceRequest;
use App\Http\Resources\NotificationPreferenceResource;
use App\Services\NotificationPreferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationPreferenceController extends BaseController
{
    public function __construct(
        protected NotificationPreferenceService $notificationPreferenceService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $notificationPreferences = $this->notificationPreferenceService->getPaginated($perPage);

            return $this->sendPaginated(
                NotificationPreferenceResource::collection($notificationPreferences),
                'NotificationPreferences başarıyla getirildi'
            );
        }

        $notificationPreferences = $this->notificationPreferenceService->getAll();

        return NotificationPreferenceResource::collection($notificationPreferences);
    }

    public function store(StoreNotificationPreferenceRequest $request): JsonResponse
    {
        $notificationPreference = $this->notificationPreferenceService->create($request->validated());

        return $this->sendSuccess(
            new NotificationPreferenceResource($notificationPreference),
            'NotificationPreference başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $notificationPreference = $this->notificationPreferenceService->findByIdOrFail($id);

        return $this->sendSuccess(
            new NotificationPreferenceResource($notificationPreference),
            'NotificationPreference başarıyla getirildi'
        );
    }

    public function update(UpdateNotificationPreferenceRequest $request, string $id): JsonResponse
    {
        $notificationPreference = $this->notificationPreferenceService->update($id, $request->validated());

        return $this->sendSuccess(
            new NotificationPreferenceResource($notificationPreference),
            'NotificationPreference başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->notificationPreferenceService->delete($id);

        return $this->sendSuccess(
            null,
            'NotificationPreference başarıyla silindi'
        );
    }
}
