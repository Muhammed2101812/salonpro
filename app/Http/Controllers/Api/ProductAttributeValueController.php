<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductAttributeValue\StoreProductAttributeValueRequest;
use App\Http\Requests\ProductAttributeValue\UpdateProductAttributeValueRequest;
use App\Http\Resources\ProductAttributeValueResource;
use App\Services\Contracts\ProductAttributeValueServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductAttributeValueController extends Controller
{
    public function __construct(
        protected ProductAttributeValueServiceInterface $attributeValueService
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $productId = $request->query('product_id');
        $attributeId = $request->query('attribute_id');

        if ($productId) {
            $values = $this->attributeValueService->getProductAttributes($productId);
        } elseif ($attributeId) {
            $values = $this->attributeValueService->getAll();
        } else {
            $perPage = (int) $request->query('per_page', 15);
            $values = $this->attributeValueService->getAll($perPage);
        }

        return ProductAttributeValueResource::collection($values);
    }

    public function store(StoreProductAttributeValueRequest $request): JsonResponse
    {
        $value = $this->attributeValueService->setProductAttribute(
            $request->input('product_id'),
            $request->input('attribute_id'),
            $request->input('attribute_value')
        );

        return response()->json([
            'message' => 'Product attribute value set successfully',
            'data' => ProductAttributeValueResource::make($value),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $value = $this->attributeValueService->findById($id);

        return response()->json([
            'data' => ProductAttributeValueResource::make($value),
        ]);
    }

    public function update(UpdateProductAttributeValueRequest $request, string $id): JsonResponse
    {
        $value = $this->attributeValueService->update($id, $request->validated());

        return response()->json([
            'message' => 'Product attribute value updated successfully',
            'data' => ProductAttributeValueResource::make($value),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->attributeValueService->delete($id);

        return response()->json([
            'message' => 'Product attribute value deleted successfully',
        ]);
    }

    public function bulkSet(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'uuid', 'exists:products,id'],
            'attributes' => ['required', 'array'],
            'attributes.*' => ['required', 'string'],
        ]);

        $results = $this->attributeValueService->bulkSetAttributes(
            $request->input('product_id'),
            $request->input('attributes')
        );

        return response()->json([
            'message' => 'Product attributes set successfully',
            'data' => ProductAttributeValueResource::collection($results),
        ]);
    }

    public function deleteProductAttribute(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'uuid', 'exists:products,id'],
            'attribute_id' => ['required', 'uuid', 'exists:product_attributes,id'],
        ]);

        $deleted = $this->attributeValueService->deleteProductAttribute(
            $request->input('product_id'),
            $request->input('attribute_id')
        );

        return response()->json([
            'message' => $deleted ? 'Product attribute deleted successfully' : 'Product attribute not found',
        ], $deleted ? 200 : 404);
    }
}
