<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreLoyaltyPointTransactionRequest;
use App\Http\Requests\UpdateLoyaltyPointTransactionRequest;
use App\Http\Resources\LoyaltyPointTransactionResource;
use App\Services\LoyaltyPointTransactionService;
use App\Models\LoyaltyPointTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LoyaltyPointTransactionController extends BaseController
{
    public function __construct(
        protected LoyaltyPointTransactionService $loyaltyPointTransactionService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', LoyaltyPointTransaction::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $loyaltyPointTransactions = $this->loyaltyPointTransactionService->getPaginated($perPage);

            return $this->sendPaginated(
                LoyaltyPointTransactionResource::collection($loyaltyPointTransactions),
                'LoyaltyPointTransactions başarıyla getirildi'
            );
        }

        $loyaltyPointTransactions = $this->loyaltyPointTransactionService->getAll();

        return LoyaltyPointTransactionResource::collection($loyaltyPointTransactions);
    }

    public function store(StoreLoyaltyPointTransactionRequest $request): JsonResponse
    {
        $this->authorize('create', LoyaltyPointTransaction::class);

        $loyaltyPointTransaction = $this->loyaltyPointTransactionService->create($request->validated());

        return $this->sendSuccess(
            new LoyaltyPointTransactionResource($loyaltyPointTransaction),
            'LoyaltyPointTransaction başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $loyaltyPointTransaction = $this->loyaltyPointTransactionService->findByIdOrFail($id);

        return $this->sendSuccess(
            new LoyaltyPointTransactionResource($loyaltyPointTransaction),
            'LoyaltyPointTransaction başarıyla getirildi'
        );
    }

    public function update(UpdateLoyaltyPointTransactionRequest $request, string $id): JsonResponse
    {
        $loyaltyPointTransaction = $this->loyaltyPointTransactionService->update($id, $request->validated());

        return $this->sendSuccess(
            new LoyaltyPointTransactionResource($loyaltyPointTransaction),
            'LoyaltyPointTransaction başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->loyaltyPointTransactionService->delete($id);

        return $this->sendSuccess(
            null,
            'LoyaltyPointTransaction başarıyla silindi'
        );
    }
}
