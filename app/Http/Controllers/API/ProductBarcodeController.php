<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreProductBarcodeRequest;
use App\Http\Requests\UpdateProductBarcodeRequest;
use App\Http\Resources\ProductBarcodeResource;
use App\Services\ProductBarcodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductBarcodeController extends BaseController
{
    public function __construct(
        protected ProductBarcodeService $productBarcodeService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $productBarcodes = $this->productBarcodeService->getPaginated($perPage);

            return $this->sendPaginated(
                ProductBarcodeResource::collection($productBarcodes),
                'ProductBarcodes başarıyla getirildi'
            );
        }

        $productBarcodes = $this->productBarcodeService->getAll();

        return ProductBarcodeResource::collection($productBarcodes);
    }

    public function store(StoreProductBarcodeRequest $request): JsonResponse
    {
        $productBarcode = $this->productBarcodeService->create($request->validated());

        return $this->sendSuccess(
            new ProductBarcodeResource($productBarcode),
            'ProductBarcode başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $productBarcode = $this->productBarcodeService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ProductBarcodeResource($productBarcode),
            'ProductBarcode başarıyla getirildi'
        );
    }

    public function update(UpdateProductBarcodeRequest $request, string $id): JsonResponse
    {
        $productBarcode = $this->productBarcodeService->update($id, $request->validated());

        return $this->sendSuccess(
            new ProductBarcodeResource($productBarcode),
            'ProductBarcode başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->productBarcodeService->delete($id);

        return $this->sendSuccess(
            null,
            'ProductBarcode başarıyla silindi'
        );
    }
}
