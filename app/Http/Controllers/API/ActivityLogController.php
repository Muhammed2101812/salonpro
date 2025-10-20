<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreActivityLogRequest;
use App\Http\Requests\UpdateActivityLogRequest;
use App\Http\Resources\ActivityLogResource;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ActivityLogController extends BaseController
{
    public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $activityLogs = $this->activityLogService->getPaginated($perPage);

            return $this->sendPaginated(
                ActivityLogResource::collection($activityLogs),
                'ActivityLogs başarıyla getirildi'
            );
        }

        $activityLogs = $this->activityLogService->getAll();

        return ActivityLogResource::collection($activityLogs);
    }

    public function store(StoreActivityLogRequest $request): JsonResponse
    {
        $activityLog = $this->activityLogService->create($request->validated());

        return $this->sendSuccess(
            new ActivityLogResource($activityLog),
            'ActivityLog başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $activityLog = $this->activityLogService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ActivityLogResource($activityLog),
            'ActivityLog başarıyla getirildi'
        );
    }

    public function update(UpdateActivityLogRequest $request, string $id): JsonResponse
    {
        $activityLog = $this->activityLogService->update($id, $request->validated());

        return $this->sendSuccess(
            new ActivityLogResource($activityLog),
            'ActivityLog başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->activityLogService->delete($id);

        return $this->sendSuccess(
            null,
            'ActivityLog başarıyla silindi'
        );
    }
}
