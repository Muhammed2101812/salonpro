<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreNotificationCampaignRequest;
use App\Http\Requests\UpdateNotificationCampaignRequest;
use App\Http\Resources\NotificationCampaignResource;
use App\Services\NotificationCampaignService;
use App\Models\NotificationCampaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationCampaignController extends BaseController
{
    public function __construct(
        protected NotificationCampaignService $notificationCampaignService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', NotificationCampaign::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $notificationCampaigns = $this->notificationCampaignService->getPaginated($perPage);

            return $this->sendPaginated(
                NotificationCampaignResource::collection($notificationCampaigns),
                'NotificationCampaigns başarıyla getirildi'
            );
        }

        $notificationCampaigns = $this->notificationCampaignService->getAll();

        return NotificationCampaignResource::collection($notificationCampaigns);
    }

    public function store(StoreNotificationCampaignRequest $request): JsonResponse
    {
        $this->authorize('create', NotificationCampaign::class);

        $notificationCampaign = $this->notificationCampaignService->create($request->validated());

        return $this->sendSuccess(
            new NotificationCampaignResource($notificationCampaign),
            'NotificationCampaign başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $notificationCampaign = $this->notificationCampaignService->findByIdOrFail($id);

        return $this->sendSuccess(
            new NotificationCampaignResource($notificationCampaign),
            'NotificationCampaign başarıyla getirildi'
        );
    }

    public function update(UpdateNotificationCampaignRequest $request, string $id): JsonResponse
    {
        $notificationCampaign = $this->notificationCampaignService->update($id, $request->validated());

        return $this->sendSuccess(
            new NotificationCampaignResource($notificationCampaign),
            'NotificationCampaign başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->notificationCampaignService->delete($id);

        return $this->sendSuccess(
            null,
            'NotificationCampaign başarıyla silindi'
        );
    }
}
