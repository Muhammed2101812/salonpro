<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreStockAlertRequest;
use App\Http\Requests\UpdateStockAlertRequest;
use App\Http\Resources\StockAlertResource;
use App\Services\StockAlertService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockAlertController extends BaseController
{
    public function __construct(
        protected StockAlertService $stockAlertService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $stockAlerts = $this->stockAlertService->getPaginated($perPage);

            return $this->sendPaginated(
                StockAlertResource::collection($stockAlerts),
                'StockAlerts başarıyla getirildi'
            );
        }

        $stockAlerts = $this->stockAlertService->getAll();

        return StockAlertResource::collection($stockAlerts);
    }

    public function store(StoreStockAlertRequest $request): JsonResponse
    {
        $stockAlert = $this->stockAlertService->create($request->validated());

        return $this->sendSuccess(
            new StockAlertResource($stockAlert),
            'StockAlert başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $stockAlert = $this->stockAlertService->findByIdOrFail($id);

        return $this->sendSuccess(
            new StockAlertResource($stockAlert),
            'StockAlert başarıyla getirildi'
        );
    }

    public function update(UpdateStockAlertRequest $request, string $id): JsonResponse
    {
        $stockAlert = $this->stockAlertService->update($id, $request->validated());

        return $this->sendSuccess(
            new StockAlertResource($stockAlert),
            'StockAlert başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->stockAlertService->delete($id);

        return $this->sendSuccess(
            null,
            'StockAlert başarıyla silindi'
        );
    }
}
