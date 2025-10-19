<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends BaseController
{
    public function __construct(protected ExpenseService $expenseService) {}

    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        if ($request->has('per_page')) {
            return $this->sendPaginated(ExpenseResource::collection($this->expenseService->getPaginated($perPage)), 'Expenses retrieved');
        }

        return ExpenseResource::collection($this->expenseService->getAll());
    }

    public function store(StoreExpenseRequest $request): JsonResponse
    {
        return $this->sendSuccess(new ExpenseResource($this->expenseService->create($request->validated())), 'Expense created', 201);
    }

    public function show(string $id): JsonResponse
    {
        return $this->sendSuccess(new ExpenseResource($this->expenseService->findByIdOrFail($id)), 'Expense retrieved');
    }

    public function update(UpdateExpenseRequest $request, string $id): JsonResponse
    {
        return $this->sendSuccess(new ExpenseResource($this->expenseService->update($id, $request->validated())), 'Expense updated');
    }

    public function destroy(string $id): JsonResponse
    {
        $this->expenseService->delete($id);

        return $this->sendSuccess(null, 'Expense deleted');
    }
}
