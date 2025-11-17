<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KpiDefinition\StoreKpiDefinitionRequest;
use App\Http\Requests\KpiDefinition\UpdateKpiDefinitionRequest;
use App\Http\Resources\KpiDefinitionResource;
use App\Services\Contracts\KpiDefinitionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class KpiDefinitionController extends Controller
{
    public function __construct(
        protected KpiDefinitionServiceInterface $kpiDefinitionService
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $kpis = $this->kpiDefinitionService->getAll($perPage);

        return KpiDefinitionResource::collection($kpis);
    }

    public function store(StoreKpiDefinitionRequest $request): JsonResponse
    {
        $kpi = $this->kpiDefinitionService->create($request->validated());

        return response()->json([
            'message' => 'KPI definition created successfully',
            'data' => KpiDefinitionResource::make($kpi),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $kpi = $this->kpiDefinitionService->findById($id);

        return response()->json([
            'data' => KpiDefinitionResource::make($kpi),
        ]);
    }

    public function update(UpdateKpiDefinitionRequest $request, string $id): JsonResponse
    {
        $kpi = $this->kpiDefinitionService->update($id, $request->validated());

        return response()->json([
            'message' => 'KPI definition updated successfully',
            'data' => KpiDefinitionResource::make($kpi),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->kpiDefinitionService->delete($id);

        return response()->json([
            'message' => 'KPI definition deleted successfully',
        ]);
    }

    public function active(): AnonymousResourceCollection
    {
        $kpis = $this->kpiDefinitionService->getActive();

        return KpiDefinitionResource::collection($kpis);
    }

    public function activate(string $id): JsonResponse
    {
        $kpi = $this->kpiDefinitionService->activate($id);

        return response()->json([
            'message' => 'KPI definition activated successfully',
            'data' => KpiDefinitionResource::make($kpi),
        ]);
    }

    public function deactivate(string $id): JsonResponse
    {
        $kpi = $this->kpiDefinitionService->deactivate($id);

        return response()->json([
            'message' => 'KPI definition deactivated successfully',
            'data' => KpiDefinitionResource::make($kpi),
        ]);
    }
}
