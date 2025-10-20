<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreProductAttributeValueRequest;
use App\Http\Requests\UpdateProductAttributeValueRequest;
use App\Http\Resources\ProductAttributeValueResource;
use App\Services\ProductAttributeValueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductAttributeValueController extends BaseController
{
    public function __construct(
        protected ProductAttributeValueService $productAttributeValueService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $productAttributeValues = $this->productAttributeValueService->getPaginated($perPage);

            return $this->sendPaginated(
                ProductAttributeValueResource::collection($productAttributeValues),
                'ProductAttributeValues başarıyla getirildi'
            );
        }

        $productAttributeValues = $this->productAttributeValueService->getAll();

        return ProductAttributeValueResource::collection($productAttributeValues);
    }

    public function store(StoreProductAttributeValueRequest $request): JsonResponse
    {
        $productAttributeValue = $this->productAttributeValueService->create($request->validated());

        return $this->sendSuccess(
            new ProductAttributeValueResource($productAttributeValue),
            'ProductAttributeValue başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $productAttributeValue = $this->productAttributeValueService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ProductAttributeValueResource($productAttributeValue),
            'ProductAttributeValue başarıyla getirildi'
        );
    }

    public function update(UpdateProductAttributeValueRequest $request, string $id): JsonResponse
    {
        $productAttributeValue = $this->productAttributeValueService->update($id, $request->validated());

        return $this->sendSuccess(
            new ProductAttributeValueResource($productAttributeValue),
            'ProductAttributeValue başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->productAttributeValueService->delete($id);

        return $this->sendSuccess(
            null,
            'ProductAttributeValue başarıyla silindi'
        );
    }
}
