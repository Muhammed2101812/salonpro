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
            $templates = $this->notificationTemplateService->getPaginated($perPage);

            return $this->sendPaginated(
                NotificationTemplateResource::collection($templates),
                'Notification templates retrieved successfully'
            );
        }

        $templates = $this->notificationTemplateService->getAll();

        return NotificationTemplateResource::collection($templates);
    }

    public function store(StoreNotificationTemplateRequest $request): JsonResponse
    {
        $template = $this->notificationTemplateService->create($request->validated());

        return $this->sendSuccess(
            new NotificationTemplateResource($template),
            'Notification template created successfully',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $template = $this->notificationTemplateService->findByIdOrFail($id);

        return $this->sendSuccess(
            new NotificationTemplateResource($template),
            'Notification template retrieved successfully'
        );
    }

    public function update(UpdateNotificationTemplateRequest $request, string $id): JsonResponse
    {
        $template = $this->notificationTemplateService->update($id, $request->validated());

        return $this->sendSuccess(
            new NotificationTemplateResource($template),
            'Notification template updated successfully'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->notificationTemplateService->delete($id);

        return $this->sendSuccess(
            null,
            'Notification template deleted successfully'
        );
    }

    public function restore(string $id): JsonResponse
    {
        $this->notificationTemplateService->restore($id);

        return $this->sendSuccess(
            null,
            'Notification template restored successfully'
        );
    }

    public function forceDestroy(string $id): JsonResponse
    {
        $this->notificationTemplateService->forceDelete($id);

        return $this->sendSuccess(
            null,
            'Notification template permanently deleted'
        );
    }
}
