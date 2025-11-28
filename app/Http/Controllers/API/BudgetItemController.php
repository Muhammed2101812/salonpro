<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreBudgetItemRequest;
use App\Http\Requests\UpdateBudgetItemRequest;
use App\Http\Resources\BudgetItemResource;
use App\Services\BudgetItemService;
use App\Models\BudgetItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BudgetItemController extends BaseController
{
    public function __construct(
        protected BudgetItemService $budgetItemService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', BudgetItem::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $budgetItems = $this->budgetItemService->getPaginated($perPage);

            return $this->sendPaginated(
                BudgetItemResource::collection($budgetItems),
                'BudgetItems başarıyla getirildi'
            );
        }

        $budgetItems = $this->budgetItemService->getAll();

        return BudgetItemResource::collection($budgetItems);
    }

    public function store(StoreBudgetItemRequest $request): JsonResponse
    {
        $this->authorize('create', BudgetItem::class);

        $budgetItem = $this->budgetItemService->create($request->validated());

        return $this->sendSuccess(
            new BudgetItemResource($budgetItem),
            'BudgetItem başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $budgetItem = $this->budgetItemService->findByIdOrFail($id);

        return $this->sendSuccess(
            new BudgetItemResource($budgetItem),
            'BudgetItem başarıyla getirildi'
        );
    }

    public function update(UpdateBudgetItemRequest $request, string $id): JsonResponse
    {
        $budgetItem = $this->budgetItemService->update($id, $request->validated());

        return $this->sendSuccess(
            new BudgetItemResource($budgetItem),
            'BudgetItem başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->budgetItemService->delete($id);

        return $this->sendSuccess(
            null,
            'BudgetItem başarıyla silindi'
        );
    }
}
