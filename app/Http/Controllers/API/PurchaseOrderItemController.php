<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StorePurchaseOrderItemRequest;
use App\Http\Requests\UpdatePurchaseOrderItemRequest;
use App\Http\Resources\PurchaseOrderItemResource;
use App\Services\PurchaseOrderItemService;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PurchaseOrderItemController extends BaseController
{
    public function __construct(
        protected PurchaseOrderItemService $purchaseOrderItemService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', PurchaseOrderItem::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $purchaseOrderItems = $this->purchaseOrderItemService->getPaginated($perPage);

            return $this->sendPaginated(
                PurchaseOrderItemResource::collection($purchaseOrderItems),
                'PurchaseOrderItems başarıyla getirildi'
            );
        }

        $purchaseOrderItems = $this->purchaseOrderItemService->getAll();

        return PurchaseOrderItemResource::collection($purchaseOrderItems);
    }

    public function store(StorePurchaseOrderItemRequest $request): JsonResponse
    {
        $this->authorize('create', PurchaseOrderItem::class);

        $purchaseOrderItem = $this->purchaseOrderItemService->create($request->validated());

        return $this->sendSuccess(
            new PurchaseOrderItemResource($purchaseOrderItem),
            'PurchaseOrderItem başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $purchaseOrderItem = $this->purchaseOrderItemService->findByIdOrFail($id);

        return $this->sendSuccess(
            new PurchaseOrderItemResource($purchaseOrderItem),
            'PurchaseOrderItem başarıyla getirildi'
        );
    }

    public function update(UpdatePurchaseOrderItemRequest $request, string $id): JsonResponse
    {
        $purchaseOrderItem = $this->purchaseOrderItemService->update($id, $request->validated());

        return $this->sendSuccess(
            new PurchaseOrderItemResource($purchaseOrderItem),
            'PurchaseOrderItem başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->purchaseOrderItemService->delete($id);

        return $this->sendSuccess(
            null,
            'PurchaseOrderItem başarıyla silindi'
        );
    }
}
