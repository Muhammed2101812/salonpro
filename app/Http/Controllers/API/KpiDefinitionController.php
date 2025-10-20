<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreKpiDefinitionRequest;
use App\Http\Requests\UpdateKpiDefinitionRequest;
use App\Http\Resources\KpiDefinitionResource;
use App\Services\KpiDefinitionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class KpiDefinitionController extends BaseController
{
    public function __construct(
        protected KpiDefinitionService $kpiDefinitionService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $kpiDefinitions = $this->kpiDefinitionService->getPaginated($perPage);

            return $this->sendPaginated(
                KpiDefinitionResource::collection($kpiDefinitions),
                'KpiDefinitions başarıyla getirildi'
            );
        }

        $kpiDefinitions = $this->kpiDefinitionService->getAll();

        return KpiDefinitionResource::collection($kpiDefinitions);
    }

    public function store(StoreKpiDefinitionRequest $request): JsonResponse
    {
        $kpiDefinition = $this->kpiDefinitionService->create($request->validated());

        return $this->sendSuccess(
            new KpiDefinitionResource($kpiDefinition),
            'KpiDefinition başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $kpiDefinition = $this->kpiDefinitionService->findByIdOrFail($id);

        return $this->sendSuccess(
            new KpiDefinitionResource($kpiDefinition),
            'KpiDefinition başarıyla getirildi'
        );
    }

    public function update(UpdateKpiDefinitionRequest $request, string $id): JsonResponse
    {
        $kpiDefinition = $this->kpiDefinitionService->update($id, $request->validated());

        return $this->sendSuccess(
            new KpiDefinitionResource($kpiDefinition),
            'KpiDefinition başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->kpiDefinitionService->delete($id);

        return $this->sendSuccess(
            null,
            'KpiDefinition başarıyla silindi'
        );
    }
}
