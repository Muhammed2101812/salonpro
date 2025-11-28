<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreStockAuditItemRequest;
use App\Http\Requests\UpdateStockAuditItemRequest;
use App\Http\Resources\StockAuditItemResource;
use App\Services\StockAuditItemService;
use App\Models\StockAuditItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockAuditItemController extends BaseController
{
    public function __construct(
        protected StockAuditItemService $stockAuditItemService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', StockAuditItem::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $stockAuditItems = $this->stockAuditItemService->getPaginated($perPage);

            return $this->sendPaginated(
                StockAuditItemResource::collection($stockAuditItems),
                'StockAuditItems başarıyla getirildi'
            );
        }

        $stockAuditItems = $this->stockAuditItemService->getAll();

        return StockAuditItemResource::collection($stockAuditItems);
    }

    public function store(StoreStockAuditItemRequest $request): JsonResponse
    {
        $this->authorize('create', StockAuditItem::class);

        $stockAuditItem = $this->stockAuditItemService->create($request->validated());

        return $this->sendSuccess(
            new StockAuditItemResource($stockAuditItem),
            'StockAuditItem başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $stockAuditItem = $this->stockAuditItemService->findByIdOrFail($id);

        return $this->sendSuccess(
            new StockAuditItemResource($stockAuditItem),
            'StockAuditItem başarıyla getirildi'
        );
    }

    public function update(UpdateStockAuditItemRequest $request, string $id): JsonResponse
    {
        $stockAuditItem = $this->stockAuditItemService->update($id, $request->validated());

        return $this->sendSuccess(
            new StockAuditItemResource($stockAuditItem),
            'StockAuditItem başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->stockAuditItemService->delete($id);

        return $this->sendSuccess(
            null,
            'StockAuditItem başarıyla silindi'
        );
    }
}
