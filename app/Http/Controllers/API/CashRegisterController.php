<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCashRegisterRequest;
use App\Http\Requests\UpdateCashRegisterRequest;
use App\Http\Resources\CashRegisterResource;
use App\Services\CashRegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CashRegisterController extends BaseController
{
    public function __construct(
        protected CashRegisterService $cashRegisterService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $cashRegisters = $this->cashRegisterService->getPaginated($perPage);

            return $this->sendPaginated(
                CashRegisterResource::collection($cashRegisters),
                'CashRegisters başarıyla getirildi'
            );
        }

        $cashRegisters = $this->cashRegisterService->getAll();

        return CashRegisterResource::collection($cashRegisters);
    }

    public function store(StoreCashRegisterRequest $request): JsonResponse
    {
        $cashRegister = $this->cashRegisterService->create($request->validated());

        return $this->sendSuccess(
            new CashRegisterResource($cashRegister),
            'CashRegister başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $cashRegister = $this->cashRegisterService->findByIdOrFail($id);

        return $this->sendSuccess(
            new CashRegisterResource($cashRegister),
            'CashRegister başarıyla getirildi'
        );
    }

    public function update(UpdateCashRegisterRequest $request, string $id): JsonResponse
    {
        $cashRegister = $this->cashRegisterService->update($id, $request->validated());

        return $this->sendSuccess(
            new CashRegisterResource($cashRegister),
            'CashRegister başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->cashRegisterService->delete($id);

        return $this->sendSuccess(
            null,
            'CashRegister başarıyla silindi'
        );
    }
}
