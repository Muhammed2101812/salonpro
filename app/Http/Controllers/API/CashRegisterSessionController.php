<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCashRegisterSessionRequest;
use App\Http\Requests\UpdateCashRegisterSessionRequest;
use App\Http\Resources\CashRegisterSessionResource;
use App\Services\CashRegisterSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CashRegisterSessionController extends BaseController
{
    public function __construct(
        protected CashRegisterSessionService $cashRegisterSessionService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $cashRegisterSessions = $this->cashRegisterSessionService->getPaginated($perPage);

            return $this->sendPaginated(
                CashRegisterSessionResource::collection($cashRegisterSessions),
                'CashRegisterSessions başarıyla getirildi'
            );
        }

        $cashRegisterSessions = $this->cashRegisterSessionService->getAll();

        return CashRegisterSessionResource::collection($cashRegisterSessions);
    }

    public function store(StoreCashRegisterSessionRequest $request): JsonResponse
    {
        $cashRegisterSession = $this->cashRegisterSessionService->create($request->validated());

        return $this->sendSuccess(
            new CashRegisterSessionResource($cashRegisterSession),
            'CashRegisterSession başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $cashRegisterSession = $this->cashRegisterSessionService->findByIdOrFail($id);

        return $this->sendSuccess(
            new CashRegisterSessionResource($cashRegisterSession),
            'CashRegisterSession başarıyla getirildi'
        );
    }

    public function update(UpdateCashRegisterSessionRequest $request, string $id): JsonResponse
    {
        $cashRegisterSession = $this->cashRegisterSessionService->update($id, $request->validated());

        return $this->sendSuccess(
            new CashRegisterSessionResource($cashRegisterSession),
            'CashRegisterSession başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->cashRegisterSessionService->delete($id);

        return $this->sendSuccess(
            null,
            'CashRegisterSession başarıyla silindi'
        );
    }
}
