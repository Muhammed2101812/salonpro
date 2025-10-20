<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreBudgetPlanRequest;
use App\Http\Requests\UpdateBudgetPlanRequest;
use App\Http\Resources\BudgetPlanResource;
use App\Services\BudgetPlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BudgetPlanController extends BaseController
{
    public function __construct(
        protected BudgetPlanService $budgetPlanService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $budgetPlans = $this->budgetPlanService->getPaginated($perPage);

            return $this->sendPaginated(
                BudgetPlanResource::collection($budgetPlans),
                'BudgetPlans başarıyla getirildi'
            );
        }

        $budgetPlans = $this->budgetPlanService->getAll();

        return BudgetPlanResource::collection($budgetPlans);
    }

    public function store(StoreBudgetPlanRequest $request): JsonResponse
    {
        $budgetPlan = $this->budgetPlanService->create($request->validated());

        return $this->sendSuccess(
            new BudgetPlanResource($budgetPlan),
            'BudgetPlan başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $budgetPlan = $this->budgetPlanService->findByIdOrFail($id);

        return $this->sendSuccess(
            new BudgetPlanResource($budgetPlan),
            'BudgetPlan başarıyla getirildi'
        );
    }

    public function update(UpdateBudgetPlanRequest $request, string $id): JsonResponse
    {
        $budgetPlan = $this->budgetPlanService->update($id, $request->validated());

        return $this->sendSuccess(
            new BudgetPlanResource($budgetPlan),
            'BudgetPlan başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->budgetPlanService->delete($id);

        return $this->sendSuccess(
            null,
            'BudgetPlan başarıyla silindi'
        );
    }
}
