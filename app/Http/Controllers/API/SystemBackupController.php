<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreSystemBackupRequest;
use App\Http\Requests\UpdateSystemBackupRequest;
use App\Http\Resources\SystemBackupResource;
use App\Services\SystemBackupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SystemBackupController extends BaseController
{
    public function __construct(
        protected SystemBackupService $systemBackupService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $systemBackups = $this->systemBackupService->getPaginated($perPage);

            return $this->sendPaginated(
                SystemBackupResource::collection($systemBackups),
                'SystemBackups başarıyla getirildi'
            );
        }

        $systemBackups = $this->systemBackupService->getAll();

        return SystemBackupResource::collection($systemBackups);
    }

    public function store(StoreSystemBackupRequest $request): JsonResponse
    {
        $systemBackup = $this->systemBackupService->create($request->validated());

        return $this->sendSuccess(
            new SystemBackupResource($systemBackup),
            'SystemBackup başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $systemBackup = $this->systemBackupService->findByIdOrFail($id);

        return $this->sendSuccess(
            new SystemBackupResource($systemBackup),
            'SystemBackup başarıyla getirildi'
        );
    }

    public function update(UpdateSystemBackupRequest $request, string $id): JsonResponse
    {
        $systemBackup = $this->systemBackupService->update($id, $request->validated());

        return $this->sendSuccess(
            new SystemBackupResource($systemBackup),
            'SystemBackup başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->systemBackupService->delete($id);

        return $this->sendSuccess(
            null,
            'SystemBackup başarıyla silindi'
        );
    }
}
