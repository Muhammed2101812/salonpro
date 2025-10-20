<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreAuditLogRequest;
use App\Http\Requests\UpdateAuditLogRequest;
use App\Http\Resources\AuditLogResource;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuditLogController extends BaseController
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $auditLogs = $this->auditLogService->getPaginated($perPage);

            return $this->sendPaginated(
                AuditLogResource::collection($auditLogs),
                'AuditLogs başarıyla getirildi'
            );
        }

        $auditLogs = $this->auditLogService->getAll();

        return AuditLogResource::collection($auditLogs);
    }

    public function store(StoreAuditLogRequest $request): JsonResponse
    {
        $auditLog = $this->auditLogService->create($request->validated());

        return $this->sendSuccess(
            new AuditLogResource($auditLog),
            'AuditLog başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $auditLog = $this->auditLogService->findByIdOrFail($id);

        return $this->sendSuccess(
            new AuditLogResource($auditLog),
            'AuditLog başarıyla getirildi'
        );
    }

    public function update(UpdateAuditLogRequest $request, string $id): JsonResponse
    {
        $auditLog = $this->auditLogService->update($id, $request->validated());

        return $this->sendSuccess(
            new AuditLogResource($auditLog),
            'AuditLog başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->auditLogService->delete($id);

        return $this->sendSuccess(
            null,
            'AuditLog başarıyla silindi'
        );
    }
}
