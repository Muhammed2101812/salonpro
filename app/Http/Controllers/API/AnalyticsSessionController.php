<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreAnalyticsSessionRequest;
use App\Http\Requests\UpdateAnalyticsSessionRequest;
use App\Http\Resources\AnalyticsSessionResource;
use App\Services\AnalyticsSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AnalyticsSessionController extends BaseController
{
    public function __construct(
        protected AnalyticsSessionService $analyticsSessionService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $analyticsSessions = $this->analyticsSessionService->getPaginated($perPage);

            return $this->sendPaginated(
                AnalyticsSessionResource::collection($analyticsSessions),
                'AnalyticsSessions başarıyla getirildi'
            );
        }

        $analyticsSessions = $this->analyticsSessionService->getAll();

        return AnalyticsSessionResource::collection($analyticsSessions);
    }

    public function store(StoreAnalyticsSessionRequest $request): JsonResponse
    {
        $analyticsSession = $this->analyticsSessionService->create($request->validated());

        return $this->sendSuccess(
            new AnalyticsSessionResource($analyticsSession),
            'AnalyticsSession başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $analyticsSession = $this->analyticsSessionService->findByIdOrFail($id);

        return $this->sendSuccess(
            new AnalyticsSessionResource($analyticsSession),
            'AnalyticsSession başarıyla getirildi'
        );
    }

    public function update(UpdateAnalyticsSessionRequest $request, string $id): JsonResponse
    {
        $analyticsSession = $this->analyticsSessionService->update($id, $request->validated());

        return $this->sendSuccess(
            new AnalyticsSessionResource($analyticsSession),
            'AnalyticsSession başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->analyticsSessionService->delete($id);

        return $this->sendSuccess(
            null,
            'AnalyticsSession başarıyla silindi'
        );
    }
}
