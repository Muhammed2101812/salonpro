<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreProductBundleRequest;
use App\Http\Requests\UpdateProductBundleRequest;
use App\Http\Resources\ProductBundleResource;
use App\Services\ProductBundleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductBundleController extends BaseController
{
    public function __construct(
        protected ProductBundleService $productBundleService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $productBundles = $this->productBundleService->getPaginated($perPage);

            return $this->sendPaginated(
                ProductBundleResource::collection($productBundles),
                'ProductBundles başarıyla getirildi'
            );
        }

        $productBundles = $this->productBundleService->getAll();

        return ProductBundleResource::collection($productBundles);
    }

    public function store(StoreProductBundleRequest $request): JsonResponse
    {
        $productBundle = $this->productBundleService->create($request->validated());

        return $this->sendSuccess(
            new ProductBundleResource($productBundle),
            'ProductBundle başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $productBundle = $this->productBundleService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ProductBundleResource($productBundle),
            'ProductBundle başarıyla getirildi'
        );
    }

    public function update(UpdateProductBundleRequest $request, string $id): JsonResponse
    {
        $productBundle = $this->productBundleService->update($id, $request->validated());

        return $this->sendSuccess(
            new ProductBundleResource($productBundle),
            'ProductBundle başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->productBundleService->delete($id);

        return $this->sendSuccess(
            null,
            'ProductBundle başarıyla silindi'
        );
    }
}
