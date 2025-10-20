<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreDashboardWidgetRequest;
use App\Http\Requests\UpdateDashboardWidgetRequest;
use App\Http\Resources\DashboardWidgetResource;
use App\Services\DashboardWidgetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DashboardWidgetController extends BaseController
{
    public function __construct(
        protected DashboardWidgetService $dashboardWidgetService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $dashboardWidgets = $this->dashboardWidgetService->getPaginated($perPage);

            return $this->sendPaginated(
                DashboardWidgetResource::collection($dashboardWidgets),
                'DashboardWidgets başarıyla getirildi'
            );
        }

        $dashboardWidgets = $this->dashboardWidgetService->getAll();

        return DashboardWidgetResource::collection($dashboardWidgets);
    }

    public function store(StoreDashboardWidgetRequest $request): JsonResponse
    {
        $dashboardWidget = $this->dashboardWidgetService->create($request->validated());

        return $this->sendSuccess(
            new DashboardWidgetResource($dashboardWidget),
            'DashboardWidget başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $dashboardWidget = $this->dashboardWidgetService->findByIdOrFail($id);

        return $this->sendSuccess(
            new DashboardWidgetResource($dashboardWidget),
            'DashboardWidget başarıyla getirildi'
        );
    }

    public function update(UpdateDashboardWidgetRequest $request, string $id): JsonResponse
    {
        $dashboardWidget = $this->dashboardWidgetService->update($id, $request->validated());

        return $this->sendSuccess(
            new DashboardWidgetResource($dashboardWidget),
            'DashboardWidget başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->dashboardWidgetService->delete($id);

        return $this->sendSuccess(
            null,
            'DashboardWidget başarıyla silindi'
        );
    }
}
