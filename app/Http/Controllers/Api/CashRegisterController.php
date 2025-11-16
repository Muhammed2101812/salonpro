<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\CashRegister\StoreCashRegisterRequest;
use App\Http\Requests\CashRegister\UpdateCashRegisterRequest;
use App\Http\Resources\CashRegisterResource;
use App\Services\Contracts\CashRegisterServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CashRegisterController extends BaseController
{
    public function __construct(
        protected CashRegisterServiceInterface $cashRegisterService
    ) {}

    /**
     * Display a listing of cash registers.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $branchId = $request->get('branch_id');

        if ($request->has('active')) {
            $registers = $this->cashRegisterService->getActive($branchId);
        } elseif ($branchId) {
            $registers = $this->cashRegisterService->getByBranch($branchId);
        } else {
            $registers = $this->cashRegisterService->getAll();
        }

        return CashRegisterResource::collection($registers);
    }

    /**
     * Store a newly created cash register.
     */
    public function store(StoreCashRegisterRequest $request): CashRegisterResource
    {
        $register = $this->cashRegisterService->create($request->validated());

        return CashRegisterResource::make($register);
    }

    /**
     * Display the specified cash register.
     */
    public function show(string $id): CashRegisterResource
    {
        $register = $this->cashRegisterService->findByIdOrFail($id);

        return CashRegisterResource::make($register);
    }

    /**
     * Update the specified cash register.
     */
    public function update(UpdateCashRegisterRequest $request, string $id): CashRegisterResource
    {
        $register = $this->cashRegisterService->update($id, $request->validated());

        return CashRegisterResource::make($register);
    }

    /**
     * Remove the specified cash register.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->cashRegisterService->delete($id);

        return response()->json(['message' => 'Cash register deleted successfully']);
    }

    /**
     * Add cash to register.
     */
    public function addCash(Request $request, string $id): CashRegisterResource
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        $register = $this->cashRegisterService->addCash(
            $id,
            $request->get('amount'),
            $request->get('note')
        );

        return CashRegisterResource::make($register);
    }

    /**
     * Remove cash from register.
     */
    public function removeCash(Request $request, string $id): CashRegisterResource
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        $register = $this->cashRegisterService->removeCash(
            $id,
            $request->get('amount'),
            $request->get('note')
        );

        return CashRegisterResource::make($register);
    }

    /**
     * Open cash register.
     */
    public function open(Request $request, string $id): CashRegisterResource
    {
        $request->validate(['opening_balance' => 'required|numeric|min:0']);

        $register = $this->cashRegisterService->openRegister(
            $id,
            $request->get('opening_balance')
        );

        return CashRegisterResource::make($register);
    }

    /**
     * Close cash register.
     */
    public function close(Request $request, string $id): CashRegisterResource
    {
        $request->validate(['closing_balance' => 'required|numeric|min:0']);

        $register = $this->cashRegisterService->closeRegister(
            $id,
            $request->get('closing_balance')
        );

        return CashRegisterResource::make($register);
    }

    /**
     * Get total balance.
     */
    public function totalBalance(Request $request): JsonResponse
    {
        $branchId = $request->get('branch_id');
        $total = $this->cashRegisterService->getTotalBalance($branchId);

        return response()->json(['total_balance' => $total]);
    }
}
