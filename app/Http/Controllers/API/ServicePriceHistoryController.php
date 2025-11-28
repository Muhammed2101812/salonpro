<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreServicePriceHistoryRequest;
use App\Http\Requests\UpdateServicePriceHistoryRequest;
use App\Http\Resources\ServicePriceHistoryResource;
use App\Services\ServicePriceHistoryService;
use App\Models\ServicePriceHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServicePriceHistoryController extends BaseController
{
    public function __construct(
        protected ServicePriceHistoryService $servicePriceHistoryService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', ServicePriceHistory::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $servicePriceHistories = $this->servicePriceHistoryService->getPaginated($perPage);

            return $this->sendPaginated(
                ServicePriceHistoryResource::collection($servicePriceHistories),
                'ServicePriceHistories başarıyla getirildi'
            );
        }

        $servicePriceHistories = $this->servicePriceHistoryService->getAll();

        return ServicePriceHistoryResource::collection($servicePriceHistories);
    }

    public function store(StoreServicePriceHistoryRequest $request): JsonResponse
    {
        $this->authorize('create', ServicePriceHistory::class);

        $servicePriceHistory = $this->servicePriceHistoryService->create($request->validated());

        return $this->sendSuccess(
            new ServicePriceHistoryResource($servicePriceHistory),
            'ServicePriceHistory başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $servicePriceHistory = $this->servicePriceHistoryService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ServicePriceHistoryResource($servicePriceHistory),
            'ServicePriceHistory başarıyla getirildi'
        );
    }

    public function update(UpdateServicePriceHistoryRequest $request, string $id): JsonResponse
    {
        $servicePriceHistory = $this->servicePriceHistoryService->update($id, $request->validated());

        return $this->sendSuccess(
            new ServicePriceHistoryResource($servicePriceHistory),
            'ServicePriceHistory başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->servicePriceHistoryService->delete($id);

        return $this->sendSuccess(
            null,
            'ServicePriceHistory başarıyla silindi'
        );
    }
}
