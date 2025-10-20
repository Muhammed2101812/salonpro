<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreProductBundleItemRequest;
use App\Http\Requests\UpdateProductBundleItemRequest;
use App\Http\Resources\ProductBundleItemResource;
use App\Services\ProductBundleItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductBundleItemController extends BaseController
{
    public function __construct(
        protected ProductBundleItemService $productBundleItemService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $productBundleItems = $this->productBundleItemService->getPaginated($perPage);

            return $this->sendPaginated(
                ProductBundleItemResource::collection($productBundleItems),
                'ProductBundleItems başarıyla getirildi'
            );
        }

        $productBundleItems = $this->productBundleItemService->getAll();

        return ProductBundleItemResource::collection($productBundleItems);
    }

    public function store(StoreProductBundleItemRequest $request): JsonResponse
    {
        $productBundleItem = $this->productBundleItemService->create($request->validated());

        return $this->sendSuccess(
            new ProductBundleItemResource($productBundleItem),
            'ProductBundleItem başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $productBundleItem = $this->productBundleItemService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ProductBundleItemResource($productBundleItem),
            'ProductBundleItem başarıyla getirildi'
        );
    }

    public function update(UpdateProductBundleItemRequest $request, string $id): JsonResponse
    {
        $productBundleItem = $this->productBundleItemService->update($id, $request->validated());

        return $this->sendSuccess(
            new ProductBundleItemResource($productBundleItem),
            'ProductBundleItem başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->productBundleItemService->delete($id);

        return $this->sendSuccess(
            null,
            'ProductBundleItem başarıyla silindi'
        );
    }
}
