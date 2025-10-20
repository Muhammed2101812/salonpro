<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreProductSupplierPriceRequest;
use App\Http\Requests\UpdateProductSupplierPriceRequest;
use App\Http\Resources\ProductSupplierPriceResource;
use App\Services\ProductSupplierPriceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductSupplierPriceController extends BaseController
{
    public function __construct(
        protected ProductSupplierPriceService $productSupplierPriceService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $productSupplierPrices = $this->productSupplierPriceService->getPaginated($perPage);

            return $this->sendPaginated(
                ProductSupplierPriceResource::collection($productSupplierPrices),
                'ProductSupplierPrices başarıyla getirildi'
            );
        }

        $productSupplierPrices = $this->productSupplierPriceService->getAll();

        return ProductSupplierPriceResource::collection($productSupplierPrices);
    }

    public function store(StoreProductSupplierPriceRequest $request): JsonResponse
    {
        $productSupplierPrice = $this->productSupplierPriceService->create($request->validated());

        return $this->sendSuccess(
            new ProductSupplierPriceResource($productSupplierPrice),
            'ProductSupplierPrice başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $productSupplierPrice = $this->productSupplierPriceService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ProductSupplierPriceResource($productSupplierPrice),
            'ProductSupplierPrice başarıyla getirildi'
        );
    }

    public function update(UpdateProductSupplierPriceRequest $request, string $id): JsonResponse
    {
        $productSupplierPrice = $this->productSupplierPriceService->update($id, $request->validated());

        return $this->sendSuccess(
            new ProductSupplierPriceResource($productSupplierPrice),
            'ProductSupplierPrice başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->productSupplierPriceService->delete($id);

        return $this->sendSuccess(
            null,
            'ProductSupplierPrice başarıyla silindi'
        );
    }
}
