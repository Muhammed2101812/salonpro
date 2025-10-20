<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreProductCategoryHierarchyRequest;
use App\Http\Requests\UpdateProductCategoryHierarchyRequest;
use App\Http\Resources\ProductCategoryHierarchyResource;
use App\Services\ProductCategoryHierarchyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductCategoryHierarchyController extends BaseController
{
    public function __construct(
        protected ProductCategoryHierarchyService $productCategoryHierarchyService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $productCategoryHierarchies = $this->productCategoryHierarchyService->getPaginated($perPage);

            return $this->sendPaginated(
                ProductCategoryHierarchyResource::collection($productCategoryHierarchies),
                'ProductCategoryHierarchies başarıyla getirildi'
            );
        }

        $productCategoryHierarchies = $this->productCategoryHierarchyService->getAll();

        return ProductCategoryHierarchyResource::collection($productCategoryHierarchies);
    }

    public function store(StoreProductCategoryHierarchyRequest $request): JsonResponse
    {
        $productCategoryHierarchy = $this->productCategoryHierarchyService->create($request->validated());

        return $this->sendSuccess(
            new ProductCategoryHierarchyResource($productCategoryHierarchy),
            'ProductCategoryHierarchy başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $productCategoryHierarchy = $this->productCategoryHierarchyService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ProductCategoryHierarchyResource($productCategoryHierarchy),
            'ProductCategoryHierarchy başarıyla getirildi'
        );
    }

    public function update(UpdateProductCategoryHierarchyRequest $request, string $id): JsonResponse
    {
        $productCategoryHierarchy = $this->productCategoryHierarchyService->update($id, $request->validated());

        return $this->sendSuccess(
            new ProductCategoryHierarchyResource($productCategoryHierarchy),
            'ProductCategoryHierarchy başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->productCategoryHierarchyService->delete($id);

        return $this->sendSuccess(
            null,
            'ProductCategoryHierarchy başarıyla silindi'
        );
    }
}
