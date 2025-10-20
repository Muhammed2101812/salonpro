<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreProductAttributeRequest;
use App\Http\Requests\UpdateProductAttributeRequest;
use App\Http\Resources\ProductAttributeResource;
use App\Services\ProductAttributeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductAttributeController extends BaseController
{
    public function __construct(
        protected ProductAttributeService $productAttributeService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $productAttributes = $this->productAttributeService->getPaginated($perPage);

            return $this->sendPaginated(
                ProductAttributeResource::collection($productAttributes),
                'ProductAttributes başarıyla getirildi'
            );
        }

        $productAttributes = $this->productAttributeService->getAll();

        return ProductAttributeResource::collection($productAttributes);
    }

    public function store(StoreProductAttributeRequest $request): JsonResponse
    {
        $productAttribute = $this->productAttributeService->create($request->validated());

        return $this->sendSuccess(
            new ProductAttributeResource($productAttribute),
            'ProductAttribute başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $productAttribute = $this->productAttributeService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ProductAttributeResource($productAttribute),
            'ProductAttribute başarıyla getirildi'
        );
    }

    public function update(UpdateProductAttributeRequest $request, string $id): JsonResponse
    {
        $productAttribute = $this->productAttributeService->update($id, $request->validated());

        return $this->sendSuccess(
            new ProductAttributeResource($productAttribute),
            'ProductAttribute başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->productAttributeService->delete($id);

        return $this->sendSuccess(
            null,
            'ProductAttribute başarıyla silindi'
        );
    }
}
