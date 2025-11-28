<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreProductDiscountRequest;
use App\Http\Requests\UpdateProductDiscountRequest;
use App\Http\Resources\ProductDiscountResource;
use App\Services\ProductDiscountService;
use App\Models\ProductDiscount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductDiscountController extends BaseController
{
    public function __construct(
        protected ProductDiscountService $productDiscountService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', ProductDiscount::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $productDiscounts = $this->productDiscountService->getPaginated($perPage);

            return $this->sendPaginated(
                ProductDiscountResource::collection($productDiscounts),
                'ProductDiscounts başarıyla getirildi'
            );
        }

        $productDiscounts = $this->productDiscountService->getAll();

        return ProductDiscountResource::collection($productDiscounts);
    }

    public function store(StoreProductDiscountRequest $request): JsonResponse
    {
        $this->authorize('create', ProductDiscount::class);

        $productDiscount = $this->productDiscountService->create($request->validated());

        return $this->sendSuccess(
            new ProductDiscountResource($productDiscount),
            'ProductDiscount başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $productDiscount = $this->productDiscountService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ProductDiscountResource($productDiscount),
            'ProductDiscount başarıyla getirildi'
        );
    }

    public function update(UpdateProductDiscountRequest $request, string $id): JsonResponse
    {
        $productDiscount = $this->productDiscountService->update($id, $request->validated());

        return $this->sendSuccess(
            new ProductDiscountResource($productDiscount),
            'ProductDiscount başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->productDiscountService->delete($id);

        return $this->sendSuccess(
            null,
            'ProductDiscount başarıyla silindi'
        );
    }
}
