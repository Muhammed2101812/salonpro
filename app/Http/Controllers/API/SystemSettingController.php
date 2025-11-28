<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreSystemSettingRequest;
use App\Http\Requests\UpdateSystemSettingRequest;
use App\Http\Resources\SystemSettingResource;
use App\Services\SystemSettingService;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SystemSettingController extends BaseController
{
    public function __construct(
        protected SystemSettingService $systemSettingService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', SystemSetting::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $systemSettings = $this->systemSettingService->getPaginated($perPage);

            return $this->sendPaginated(
                SystemSettingResource::collection($systemSettings),
                'SystemSettings başarıyla getirildi'
            );
        }

        $systemSettings = $this->systemSettingService->getAll();

        return SystemSettingResource::collection($systemSettings);
    }

    public function store(StoreSystemSettingRequest $request): JsonResponse
    {
        $this->authorize('create', SystemSetting::class);

        $systemSetting = $this->systemSettingService->create($request->validated());

        return $this->sendSuccess(
            new SystemSettingResource($systemSetting),
            'SystemSetting başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $systemSetting = $this->systemSettingService->findByIdOrFail($id);

        return $this->sendSuccess(
            new SystemSettingResource($systemSetting),
            'SystemSetting başarıyla getirildi'
        );
    }

    public function update(UpdateSystemSettingRequest $request, string $id): JsonResponse
    {
        $systemSetting = $this->systemSettingService->update($id, $request->validated());

        return $this->sendSuccess(
            new SystemSettingResource($systemSetting),
            'SystemSetting başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->systemSettingService->delete($id);

        return $this->sendSuccess(
            null,
            'SystemSetting başarıyla silindi'
        );
    }
}
