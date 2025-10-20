<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreProductStockHistoryRequest;
use App\Http\Requests\UpdateProductStockHistoryRequest;
use App\Http\Resources\ProductStockHistoryResource;
use App\Services\ProductStockHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductStockHistoryController extends BaseController
{
    public function __construct(
        protected ProductStockHistoryService $productStockHistoryService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $productStockHistories = $this->productStockHistoryService->getPaginated($perPage);

            return $this->sendPaginated(
                ProductStockHistoryResource::collection($productStockHistories),
                'ProductStockHistories başarıyla getirildi'
            );
        }

        $productStockHistories = $this->productStockHistoryService->getAll();

        return ProductStockHistoryResource::collection($productStockHistories);
    }

    public function store(StoreProductStockHistoryRequest $request): JsonResponse
    {
        $productStockHistory = $this->productStockHistoryService->create($request->validated());

        return $this->sendSuccess(
            new ProductStockHistoryResource($productStockHistory),
            'ProductStockHistory başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $productStockHistory = $this->productStockHistoryService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ProductStockHistoryResource($productStockHistory),
            'ProductStockHistory başarıyla getirildi'
        );
    }

    public function update(UpdateProductStockHistoryRequest $request, string $id): JsonResponse
    {
        $productStockHistory = $this->productStockHistoryService->update($id, $request->validated());

        return $this->sendSuccess(
            new ProductStockHistoryResource($productStockHistory),
            'ProductStockHistory başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->productStockHistoryService->delete($id);

        return $this->sendSuccess(
            null,
            'ProductStockHistory başarıyla silindi'
        );
    }
}
