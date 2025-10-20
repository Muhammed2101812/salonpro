<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreProductImageRequest;
use App\Http\Requests\UpdateProductImageRequest;
use App\Http\Resources\ProductImageResource;
use App\Services\ProductImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductImageController extends BaseController
{
    public function __construct(
        protected ProductImageService $productImageService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $productImages = $this->productImageService->getPaginated($perPage);

            return $this->sendPaginated(
                ProductImageResource::collection($productImages),
                'ProductImages başarıyla getirildi'
            );
        }

        $productImages = $this->productImageService->getAll();

        return ProductImageResource::collection($productImages);
    }

    public function store(StoreProductImageRequest $request): JsonResponse
    {
        $productImage = $this->productImageService->create($request->validated());

        return $this->sendSuccess(
            new ProductImageResource($productImage),
            'ProductImage başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $productImage = $this->productImageService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ProductImageResource($productImage),
            'ProductImage başarıyla getirildi'
        );
    }

    public function update(UpdateProductImageRequest $request, string $id): JsonResponse
    {
        $productImage = $this->productImageService->update($id, $request->validated());

        return $this->sendSuccess(
            new ProductImageResource($productImage),
            'ProductImage başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->productImageService->delete($id);

        return $this->sendSuccess(
            null,
            'ProductImage başarıyla silindi'
        );
    }
}
