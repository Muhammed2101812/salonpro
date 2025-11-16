<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductVariantResource;
use App\Services\Contracts\ProductVariantServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductVariantController extends Controller
{
    public function __construct(
        private ProductVariantServiceInterface $productVariantService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', \App\Models\ProductVariant::class);

        if ($request->has('product_id')) {
            $variants = $this->productVariantService->getProductVariants($request->input('product_id'));
        } elseif ($request->has('low_stock')) {
            $threshold = $request->input('threshold', 10);
            $variants = $this->productVariantService->getLowStockVariants($threshold);
        } else {
            $variants = \App\Models\ProductVariant::with('product')->paginate($request->input('per_page', 15));
        }

        return ProductVariantResource::collection($variants);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', \App\Models\ProductVariant::class);

        $validated = $request->validate([
            'product_id' => 'required|uuid|exists:products,id',
            'sku' => 'nullable|string|unique:product_variants,sku',
            'barcode' => 'nullable|string|unique:product_variants,barcode',
            'variant_name' => 'required|string|max:255',
            'attributes' => 'nullable|json',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $variant = $this->productVariantService->createVariant(
            $validated['product_id'],
            $validated
        );

        return ProductVariantResource::make($variant)->response()->setStatusCode(201);
    }

    public function show(string $id): ProductVariantResource
    {
        $variant = \App\Models\ProductVariant::with('product')->findOrFail($id);
        $this->authorize('view', $variant);

        return ProductVariantResource::make($variant);
    }

    public function update(Request $request, string $id): ProductVariantResource
    {
        $variant = \App\Models\ProductVariant::findOrFail($id);
        $this->authorize('update', $variant);

        $validated = $request->validate([
            'variant_name' => 'sometimes|string|max:255',
            'attributes' => 'sometimes|json',
            'price' => 'sometimes|numeric|min:0',
            'cost_price' => 'sometimes|numeric|min:0',
            'reorder_level' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $updated = $this->productVariantService->updateVariant($id, $validated);

        return ProductVariantResource::make($updated);
    }

    public function destroy(string $id): JsonResponse
    {
        $variant = \App\Models\ProductVariant::findOrFail($id);
        $this->authorize('delete', $variant);

        try {
            $this->productVariantService->deleteVariant($id);
            return response()->json(['message' => 'Product variant deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function findBySku(string $sku): ProductVariantResource|JsonResponse
    {
        $this->authorize('viewAny', \App\Models\ProductVariant::class);

        $variant = $this->productVariantService->findBySku($sku);

        if (!$variant) {
            return response()->json(['message' => 'Variant not found'], 404);
        }

        return ProductVariantResource::make($variant);
    }

    public function findByBarcode(string $barcode): ProductVariantResource|JsonResponse
    {
        $this->authorize('viewAny', \App\Models\ProductVariant::class);

        $variant = $this->productVariantService->findByBarcode($barcode);

        if (!$variant) {
            return response()->json(['message' => 'Variant not found'], 404);
        }

        return ProductVariantResource::make($variant);
    }

    public function updateStock(Request $request, string $id): ProductVariantResource|JsonResponse
    {
        $variant = \App\Models\ProductVariant::findOrFail($id);
        $this->authorize('update', $variant);

        $validated = $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:add,subtract,set',
        ]);

        try {
            $updated = $this->productVariantService->updateStock(
                $id,
                $validated['quantity'],
                $validated['type']
            );

            return ProductVariantResource::make($updated);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function checkStock(Request $request, string $id): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\ProductVariant::class);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $available = $this->productVariantService->checkStock($id, $validated['quantity']);

        return response()->json([
            'variant_id' => $id,
            'requested_quantity' => $validated['quantity'],
            'available' => $available,
        ]);
    }
}
