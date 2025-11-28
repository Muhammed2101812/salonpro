<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\BankAccount\StoreBankAccountRequest;
use App\Http\Requests\BankAccount\UpdateBankAccountRequest;
use App\Http\Resources\BankAccountResource;
use App\Services\Contracts\BankAccountServiceInterface;
use App\Models\BankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BankAccountController extends BaseController
{
    public function __construct(
        protected BankAccountServiceInterface $bankAccountService
    ) {}

    /**
     * Display a listing of bank accounts.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', BankAccount::class);

        $branchId = $request->get('branch_id');

        if ($request->has('active')) {
            $accounts = $this->bankAccountService->getActive($branchId);
        } elseif ($branchId) {
            $accounts = $this->bankAccountService->getByBranch($branchId);
        } else {
            $accounts = $this->bankAccountService->getAll();
        }

        return BankAccountResource::collection($accounts);
    }

    /**
     * Store a newly created bank account.
     */
    public function store(StoreBankAccountRequest $request): BankAccountResource
    {
        $this->authorize('create', BankAccount::class);

        $account = $this->bankAccountService->create($request->validated());

        return BankAccountResource::make($account);
    }

    /**
     * Display the specified bank account.
     */
    public function show(string $id): BankAccountResource
    {
        $account = $this->bankAccountService->findByIdOrFail($id);

        return BankAccountResource::make($account);
    }

    /**
     * Update the specified bank account.
     */
    public function update(UpdateBankAccountRequest $request, string $id): BankAccountResource
    {
        $account = $this->bankAccountService->update($id, $request->validated());

        return BankAccountResource::make($account);
    }

    /**
     * Remove the specified bank account.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->bankAccountService->delete($id);

        return response()->json(['message' => 'Bank account deleted successfully']);
    }

    /**
     * Deposit money to bank account.
     */
    public function deposit(Request $request, string $id): BankAccountResource
    {
        $request->validate(['amount' => 'required|numeric|min:0.01']);

        $account = $this->bankAccountService->deposit($id, $request->get('amount'));

        return BankAccountResource::make($account);
    }

    /**
     * Withdraw money from bank account.
     */
    public function withdraw(Request $request, string $id): BankAccountResource
    {
        $request->validate(['amount' => 'required|numeric|min:0.01']);

        $account = $this->bankAccountService->withdraw($id, $request->get('amount'));

        return BankAccountResource::make($account);
    }

    /**
     * Get total balance.
     */
    public function totalBalance(Request $request): JsonResponse
    {
        $branchId = $request->get('branch_id');
        $total = $this->bankAccountService->getTotalBalance($branchId);

        return response()->json(['total_balance' => $total]);
    }

    /**
     * Activate bank account.
     */
    public function activate(string $id): BankAccountResource
    {
        $account = $this->bankAccountService->activate($id);

        return BankAccountResource::make($account);
    }

    /**
     * Deactivate bank account.
     */
    public function deactivate(string $id): BankAccountResource
    {
        $account = $this->bankAccountService->deactivate($id);

        return BankAccountResource::make($account);
    }
}
