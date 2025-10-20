<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreStockTransferRequest;
use App\Http\Requests\UpdateStockTransferRequest;
use App\Http\Resources\StockTransferResource;
use App\Services\StockTransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockTransferController extends BaseController
{
    public function __construct(
        protected StockTransferService $stockTransferService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $stockTransfers = $this->stockTransferService->getPaginated($perPage);

            return $this->sendPaginated(
                StockTransferResource::collection($stockTransfers),
                'StockTransfers başarıyla getirildi'
            );
        }

        $stockTransfers = $this->stockTransferService->getAll();

        return StockTransferResource::collection($stockTransfers);
    }

    public function store(StoreStockTransferRequest $request): JsonResponse
    {
        $stockTransfer = $this->stockTransferService->create($request->validated());

        return $this->sendSuccess(
            new StockTransferResource($stockTransfer),
            'StockTransfer başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $stockTransfer = $this->stockTransferService->findByIdOrFail($id);

        return $this->sendSuccess(
            new StockTransferResource($stockTransfer),
            'StockTransfer başarıyla getirildi'
        );
    }

    public function update(UpdateStockTransferRequest $request, string $id): JsonResponse
    {
        $stockTransfer = $this->stockTransferService->update($id, $request->validated());

        return $this->sendSuccess(
            new StockTransferResource($stockTransfer),
            'StockTransfer başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->stockTransferService->delete($id);

        return $this->sendSuccess(
            null,
            'StockTransfer başarıyla silindi'
        );
    }
}
