<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StorePerformanceMetricRequest;
use App\Http\Requests\UpdatePerformanceMetricRequest;
use App\Http\Resources\PerformanceMetricResource;
use App\Services\PerformanceMetricService;
use App\Models\PerformanceMetric;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PerformanceMetricController extends BaseController
{
    public function __construct(
        protected PerformanceMetricService $performanceMetricService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', PerformanceMetric::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $performanceMetrics = $this->performanceMetricService->getPaginated($perPage);

            return $this->sendPaginated(
                PerformanceMetricResource::collection($performanceMetrics),
                'PerformanceMetrics başarıyla getirildi'
            );
        }

        $performanceMetrics = $this->performanceMetricService->getAll();

        return PerformanceMetricResource::collection($performanceMetrics);
    }

    public function store(StorePerformanceMetricRequest $request): JsonResponse
    {
        $this->authorize('create', PerformanceMetric::class);

        $performanceMetric = $this->performanceMetricService->create($request->validated());

        return $this->sendSuccess(
            new PerformanceMetricResource($performanceMetric),
            'PerformanceMetric başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $performanceMetric = $this->performanceMetricService->findByIdOrFail($id);

        return $this->sendSuccess(
            new PerformanceMetricResource($performanceMetric),
            'PerformanceMetric başarıyla getirildi'
        );
    }

    public function update(UpdatePerformanceMetricRequest $request, string $id): JsonResponse
    {
        $performanceMetric = $this->performanceMetricService->update($id, $request->validated());

        return $this->sendSuccess(
            new PerformanceMetricResource($performanceMetric),
            'PerformanceMetric başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->performanceMetricService->delete($id);

        return $this->sendSuccess(
            null,
            'PerformanceMetric başarıyla silindi'
        );
    }
}
