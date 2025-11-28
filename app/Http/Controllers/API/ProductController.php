<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ProductService $productService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', Product::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $products = $this->productService->getPaginated($perPage);

            return $this->sendPaginated(
                ProductResource::collection($products),
                'Products retrieved successfully'
            );
        }

        $products = $this->productService->getAll();

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $this->authorize('create', Product::class);

        $product = $this->productService->create($request->validated());

        $this->authorize('view', $product);


        $this->authorize('view', $product);


        return $this->sendSuccess(
            new ProductResource($product),
            'Product created successfully',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $product = $this->productService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ProductResource($product),
            'Product retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id): JsonResponse
    {
        $product = $this->productService->update($id, $request->validated());

        $this->authorize('update', $product);


        return $this->sendSuccess(
            new ProductResource($product),
            'Product updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id): JsonResponse
    {
        $product = $this->productService->findByIdOrFail($id);

        $this->authorize('delete', $product);

        $this->productService->delete($id);

        return $this->sendSuccess(
            null,
            'Product deleted successfully'
        );
    }

    /**
     * Restore a soft-deleted resource.
     */
    public function restore(string $id): JsonResponse
    {
        $product = $this->productService->findByIdOrFail($id);

        $this->authorize('restore', $product);

        $this->productService->restore($id);

        return $this->sendSuccess(
            null,
            'Product restored successfully'
        );
    }

    /**
     * Permanently remove the specified resource from storage.
     */
    public function forceDestroy(string $id): JsonResponse
    {
        $product = $this->productService->findByIdOrFail($id);

        $this->authorize('forceDelete', $product);

        $this->productService->forceDelete($id);

        return $this->sendSuccess(
            null,
            'Product permanently deleted'
        );
    }
}
