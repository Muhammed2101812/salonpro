<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreBankAccountRequest;
use App\Http\Requests\UpdateBankAccountRequest;
use App\Http\Resources\BankAccountResource;
use App\Services\BankAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BankAccountController extends BaseController
{
    public function __construct(
        protected BankAccountService $bankAccountService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $bankAccounts = $this->bankAccountService->getPaginated($perPage);

            return $this->sendPaginated(
                BankAccountResource::collection($bankAccounts),
                'BankAccounts başarıyla getirildi'
            );
        }

        $bankAccounts = $this->bankAccountService->getAll();

        return BankAccountResource::collection($bankAccounts);
    }

    public function store(StoreBankAccountRequest $request): JsonResponse
    {
        $bankAccount = $this->bankAccountService->create($request->validated());

        return $this->sendSuccess(
            new BankAccountResource($bankAccount),
            'BankAccount başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $bankAccount = $this->bankAccountService->findByIdOrFail($id);

        return $this->sendSuccess(
            new BankAccountResource($bankAccount),
            'BankAccount başarıyla getirildi'
        );
    }

    public function update(UpdateBankAccountRequest $request, string $id): JsonResponse
    {
        $bankAccount = $this->bankAccountService->update($id, $request->validated());

        return $this->sendSuccess(
            new BankAccountResource($bankAccount),
            'BankAccount başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->bankAccountService->delete($id);

        return $this->sendSuccess(
            null,
            'BankAccount başarıyla silindi'
        );
    }
}
