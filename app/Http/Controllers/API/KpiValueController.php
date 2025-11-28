<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreKpiValueRequest;
use App\Http\Requests\UpdateKpiValueRequest;
use App\Http\Resources\KpiValueResource;
use App\Services\KpiValueService;
use App\Models\KpiValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class KpiValueController extends BaseController
{
    public function __construct(
        protected KpiValueService $kpiValueService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', KpiValue::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $kpiValues = $this->kpiValueService->getPaginated($perPage);

            return $this->sendPaginated(
                KpiValueResource::collection($kpiValues),
                'KpiValues başarıyla getirildi'
            );
        }

        $kpiValues = $this->kpiValueService->getAll();

        return KpiValueResource::collection($kpiValues);
    }

    public function store(StoreKpiValueRequest $request): JsonResponse
    {
        $this->authorize('create', KpiValue::class);

        $kpiValue = $this->kpiValueService->create($request->validated());

        return $this->sendSuccess(
            new KpiValueResource($kpiValue),
            'KpiValue başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $kpiValue = $this->kpiValueService->findByIdOrFail($id);

        return $this->sendSuccess(
            new KpiValueResource($kpiValue),
            'KpiValue başarıyla getirildi'
        );
    }

    public function update(UpdateKpiValueRequest $request, string $id): JsonResponse
    {
        $kpiValue = $this->kpiValueService->update($id, $request->validated());

        return $this->sendSuccess(
            new KpiValueResource($kpiValue),
            'KpiValue başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->kpiValueService->delete($id);

        return $this->sendSuccess(
            null,
            'KpiValue başarıyla silindi'
        );
    }
}
