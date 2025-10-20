<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreAnalyticsEventRequest;
use App\Http\Requests\UpdateAnalyticsEventRequest;
use App\Http\Resources\AnalyticsEventResource;
use App\Services\AnalyticsEventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AnalyticsEventController extends BaseController
{
    public function __construct(
        protected AnalyticsEventService $analyticsEventService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $analyticsEvents = $this->analyticsEventService->getPaginated($perPage);

            return $this->sendPaginated(
                AnalyticsEventResource::collection($analyticsEvents),
                'AnalyticsEvents başarıyla getirildi'
            );
        }

        $analyticsEvents = $this->analyticsEventService->getAll();

        return AnalyticsEventResource::collection($analyticsEvents);
    }

    public function store(StoreAnalyticsEventRequest $request): JsonResponse
    {
        $analyticsEvent = $this->analyticsEventService->create($request->validated());

        return $this->sendSuccess(
            new AnalyticsEventResource($analyticsEvent),
            'AnalyticsEvent başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $analyticsEvent = $this->analyticsEventService->findByIdOrFail($id);

        return $this->sendSuccess(
            new AnalyticsEventResource($analyticsEvent),
            'AnalyticsEvent başarıyla getirildi'
        );
    }

    public function update(UpdateAnalyticsEventRequest $request, string $id): JsonResponse
    {
        $analyticsEvent = $this->analyticsEventService->update($id, $request->validated());

        return $this->sendSuccess(
            new AnalyticsEventResource($analyticsEvent),
            'AnalyticsEvent başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->analyticsEventService->delete($id);

        return $this->sendSuccess(
            null,
            'AnalyticsEvent başarıyla silindi'
        );
    }
}
