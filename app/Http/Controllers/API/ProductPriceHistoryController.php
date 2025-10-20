<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreProductPriceHistoryRequest;
use App\Http\Requests\UpdateProductPriceHistoryRequest;
use App\Http\Resources\ProductPriceHistoryResource;
use App\Services\ProductPriceHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductPriceHistoryController extends BaseController
{
    public function __construct(
        protected ProductPriceHistoryService $productPriceHistoryService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $productPriceHistories = $this->productPriceHistoryService->getPaginated($perPage);

            return $this->sendPaginated(
                ProductPriceHistoryResource::collection($productPriceHistories),
                'ProductPriceHistories başarıyla getirildi'
            );
        }

        $productPriceHistories = $this->productPriceHistoryService->getAll();

        return ProductPriceHistoryResource::collection($productPriceHistories);
    }

    public function store(StoreProductPriceHistoryRequest $request): JsonResponse
    {
        $productPriceHistory = $this->productPriceHistoryService->create($request->validated());

        return $this->sendSuccess(
            new ProductPriceHistoryResource($productPriceHistory),
            'ProductPriceHistory başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $productPriceHistory = $this->productPriceHistoryService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ProductPriceHistoryResource($productPriceHistory),
            'ProductPriceHistory başarıyla getirildi'
        );
    }

    public function update(UpdateProductPriceHistoryRequest $request, string $id): JsonResponse
    {
        $productPriceHistory = $this->productPriceHistoryService->update($id, $request->validated());

        return $this->sendSuccess(
            new ProductPriceHistoryResource($productPriceHistory),
            'ProductPriceHistory başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->productPriceHistoryService->delete($id);

        return $this->sendSuccess(
            null,
            'ProductPriceHistory başarıyla silindi'
        );
    }
}
