<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCashRegisterTransactionRequest;
use App\Http\Requests\UpdateCashRegisterTransactionRequest;
use App\Http\Resources\CashRegisterTransactionResource;
use App\Services\CashRegisterTransactionService;
use App\Models\CashRegisterTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CashRegisterTransactionController extends BaseController
{
    public function __construct(
        protected CashRegisterTransactionService $cashRegisterTransactionService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', CashRegisterTransaction::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $cashRegisterTransactions = $this->cashRegisterTransactionService->getPaginated($perPage);

            return $this->sendPaginated(
                CashRegisterTransactionResource::collection($cashRegisterTransactions),
                'CashRegisterTransactions başarıyla getirildi'
            );
        }

        $cashRegisterTransactions = $this->cashRegisterTransactionService->getAll();

        return CashRegisterTransactionResource::collection($cashRegisterTransactions);
    }

    public function store(StoreCashRegisterTransactionRequest $request): JsonResponse
    {
        $this->authorize('create', CashRegisterTransaction::class);

        $cashRegisterTransaction = $this->cashRegisterTransactionService->create($request->validated());

        return $this->sendSuccess(
            new CashRegisterTransactionResource($cashRegisterTransaction),
            'CashRegisterTransaction başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $cashRegisterTransaction = $this->cashRegisterTransactionService->findByIdOrFail($id);

        $this->authorize('view', $cashRegisterTransaction);

        return $this->sendSuccess(
            new CashRegisterTransactionResource($cashRegisterTransaction),
            'CashRegisterTransaction başarıyla getirildi'
        );
    }

    public function update(UpdateCashRegisterTransactionRequest $request, string $id): JsonResponse
    {
        $cashRegisterTransaction = $this->cashRegisterTransactionService->findByIdOrFail($id);

        $this->authorize('update', $cashRegisterTransaction);

        $cashRegisterTransaction = $this->cashRegisterTransactionService->update($id, $request->validated());

        return $this->sendSuccess(
            new CashRegisterTransactionResource($cashRegisterTransaction),
            'CashRegisterTransaction başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $cashRegisterTransaction = $this->cashRegisterTransactionService->findByIdOrFail($id);

        $this->authorize('delete', $cashRegisterTransaction);

        $this->cashRegisterTransactionService->delete($id);

        return $this->sendSuccess(
            null,
            'CashRegisterTransaction başarıyla silindi'
        );
    }
}
