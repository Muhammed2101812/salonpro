<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreBankTransactionRequest;
use App\Http\Requests\UpdateBankTransactionRequest;
use App\Http\Resources\BankTransactionResource;
use App\Services\BankTransactionService;
use App\Models\BankTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BankTransactionController extends BaseController
{
    public function __construct(
        protected BankTransactionService $bankTransactionService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', BankTransaction::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $bankTransactions = $this->bankTransactionService->getPaginated($perPage);

            return $this->sendPaginated(
                BankTransactionResource::collection($bankTransactions),
                'BankTransactions başarıyla getirildi'
            );
        }

        $bankTransactions = $this->bankTransactionService->getAll();

        return BankTransactionResource::collection($bankTransactions);
    }

    public function store(StoreBankTransactionRequest $request): JsonResponse
    {
        $this->authorize('create', BankTransaction::class);

        $bankTransaction = $this->bankTransactionService->create($request->validated());

        return $this->sendSuccess(
            new BankTransactionResource($bankTransaction),
            'BankTransaction başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $bankTransaction = $this->bankTransactionService->findByIdOrFail($id);

        return $this->sendSuccess(
            new BankTransactionResource($bankTransaction),
            'BankTransaction başarıyla getirildi'
        );
    }

    public function update(UpdateBankTransactionRequest $request, string $id): JsonResponse
    {
        $bankTransaction = $this->bankTransactionService->update($id, $request->validated());

        return $this->sendSuccess(
            new BankTransactionResource($bankTransaction),
            'BankTransaction başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->bankTransactionService->delete($id);

        return $this->sendSuccess(
            null,
            'BankTransaction başarıyla silindi'
        );
    }
}
