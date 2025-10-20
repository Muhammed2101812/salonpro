<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreProductVariantRequest;
use App\Http\Requests\UpdateProductVariantRequest;
use App\Http\Resources\ProductVariantResource;
use App\Services\ProductVariantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductVariantController extends BaseController
{
    public function __construct(
        protected ProductVariantService $productVariantService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $productVariants = $this->productVariantService->getPaginated($perPage);

            return $this->sendPaginated(
                ProductVariantResource::collection($productVariants),
                'ProductVariants başarıyla getirildi'
            );
        }

        $productVariants = $this->productVariantService->getAll();

        return ProductVariantResource::collection($productVariants);
    }

    public function store(StoreProductVariantRequest $request): JsonResponse
    {
        $productVariant = $this->productVariantService->create($request->validated());

        return $this->sendSuccess(
            new ProductVariantResource($productVariant),
            'ProductVariant başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $productVariant = $this->productVariantService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ProductVariantResource($productVariant),
            'ProductVariant başarıyla getirildi'
        );
    }

    public function update(UpdateProductVariantRequest $request, string $id): JsonResponse
    {
        $productVariant = $this->productVariantService->update($id, $request->validated());

        return $this->sendSuccess(
            new ProductVariantResource($productVariant),
            'ProductVariant başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->productVariantService->delete($id);

        return $this->sendSuccess(
            null,
            'ProductVariant başarıyla silindi'
        );
    }
}
