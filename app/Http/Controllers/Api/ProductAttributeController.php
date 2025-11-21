<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductAttribute\StoreProductAttributeRequest;
use App\Http\Requests\ProductAttribute\UpdateProductAttributeRequest;
use App\Http\Resources\ProductAttributeResource;
use App\Services\Contracts\ProductAttributeServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductAttributeController extends Controller
{
    public function __construct(
        protected ProductAttributeServiceInterface $attributeService
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $attributes = $this->attributeService->getAll($perPage);

        return ProductAttributeResource::collection($attributes);
    }

    public function store(StoreProductAttributeRequest $request): JsonResponse
    {
        $attribute = $this->attributeService->create($request->validated());

        return response()->json([
            'message' => 'Product attribute created successfully',
            'data' => ProductAttributeResource::make($attribute),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $attribute = $this->attributeService->findById($id);

        return response()->json([
            'data' => ProductAttributeResource::make($attribute),
        ]);
    }

    public function update(UpdateProductAttributeRequest $request, string $id): JsonResponse
    {
        $attribute = $this->attributeService->update($id, $request->validated());

        return response()->json([
            'message' => 'Product attribute updated successfully',
            'data' => ProductAttributeResource::make($attribute),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->attributeService->delete($id);

        return response()->json([
            'message' => 'Product attribute deleted successfully',
        ]);
    }

    public function filterable(): AnonymousResourceCollection
    {
        $attributes = $this->attributeService->getFilterable();

        return ProductAttributeResource::collection($attributes);
    }

    public function required(): AnonymousResourceCollection
    {
        $attributes = $this->attributeService->getRequired();

        return ProductAttributeResource::collection($attributes);
    }

    public function sorted(): AnonymousResourceCollection
    {
        $attributes = $this->attributeService->getAllSorted();

        return ProductAttributeResource::collection($attributes);
    }
}
