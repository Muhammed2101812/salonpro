<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StorePurchaseOrderRequest;
use App\Http\Requests\UpdatePurchaseOrderRequest;
use App\Http\Resources\PurchaseOrderResource;
use App\Services\PurchaseOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PurchaseOrderController extends BaseController
{
    public function __construct(
        protected PurchaseOrderService $purchaseOrderService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $purchaseOrders = $this->purchaseOrderService->getPaginated($perPage);

            return $this->sendPaginated(
                PurchaseOrderResource::collection($purchaseOrders),
                'PurchaseOrders başarıyla getirildi'
            );
        }

        $purchaseOrders = $this->purchaseOrderService->getAll();

        return PurchaseOrderResource::collection($purchaseOrders);
    }

    public function store(StorePurchaseOrderRequest $request): JsonResponse
    {
        $purchaseOrder = $this->purchaseOrderService->create($request->validated());

        return $this->sendSuccess(
            new PurchaseOrderResource($purchaseOrder),
            'PurchaseOrder başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $purchaseOrder = $this->purchaseOrderService->findByIdOrFail($id);

        return $this->sendSuccess(
            new PurchaseOrderResource($purchaseOrder),
            'PurchaseOrder başarıyla getirildi'
        );
    }

    public function update(UpdatePurchaseOrderRequest $request, string $id): JsonResponse
    {
        $purchaseOrder = $this->purchaseOrderService->update($id, $request->validated());

        return $this->sendSuccess(
            new PurchaseOrderResource($purchaseOrder),
            'PurchaseOrder başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->purchaseOrderService->delete($id);

        return $this->sendSuccess(
            null,
            'PurchaseOrder başarıyla silindi'
        );
    }
}
