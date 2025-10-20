<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreNotificationTemplateRequest;
use App\Http\Requests\UpdateNotificationTemplateRequest;
use App\Http\Resources\NotificationTemplateResource;
use App\Services\NotificationTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationTemplateController extends BaseController
{
    public function __construct(
        protected NotificationTemplateService $notificationTemplateService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $notificationTemplates = $this->notificationTemplateService->getPaginated($perPage);

            return $this->sendPaginated(
                NotificationTemplateResource::collection($notificationTemplates),
                'NotificationTemplates başarıyla getirildi'
            );
        }

        $notificationTemplates = $this->notificationTemplateService->getAll();

        return NotificationTemplateResource::collection($notificationTemplates);
    }

    public function store(StoreNotificationTemplateRequest $request): JsonResponse
    {
        $notificationTemplate = $this->notificationTemplateService->create($request->validated());

        return $this->sendSuccess(
            new NotificationTemplateResource($notificationTemplate),
            'NotificationTemplate başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $notificationTemplate = $this->notificationTemplateService->findByIdOrFail($id);

        return $this->sendSuccess(
            new NotificationTemplateResource($notificationTemplate),
            'NotificationTemplate başarıyla getirildi'
        );
    }

    public function update(UpdateNotificationTemplateRequest $request, string $id): JsonResponse
    {
        $notificationTemplate = $this->notificationTemplateService->update($id, $request->validated());

        return $this->sendSuccess(
            new NotificationTemplateResource($notificationTemplate),
            'NotificationTemplate başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->notificationTemplateService->delete($id);

        return $this->sendSuccess(
            null,
            'NotificationTemplate başarıyla silindi'
        );
    }
}
