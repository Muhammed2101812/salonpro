<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\BankAccountRepositoryInterface;
use App\Services\Contracts\BankAccountServiceInterface;
use Illuminate\Support\Facades\DB;

class BankAccountService extends BaseService implements BankAccountServiceInterface
{
    public function __construct(
        protected BankAccountRepositoryInterface $bankAccountRepository
    ) {
        parent::__construct($bankAccountRepository);
    }

    public function getByBranch(string $branchId): mixed
    {
        return $this->bankAccountRepository->findByBranch($branchId);
    }

    public function getActive(?string $branchId = null): mixed
    {
        return $this->bankAccountRepository->findActive($branchId);
    }

    public function deposit(string $id, float $amount): mixed
    {
        return DB::transaction(function () use ($id, $amount) {
            if ($amount <= 0) {
                throw new \InvalidArgumentException('Deposit amount must be greater than zero');
            }

            return $this->bankAccountRepository->updateBalance($id, $amount, 'add');
        });
    }

    public function withdraw(string $id, float $amount): mixed
    {
        return DB::transaction(function () use ($id, $amount) {
            if ($amount <= 0) {
                throw new \InvalidArgumentException('Withdrawal amount must be greater than zero');
            }

            $account = $this->bankAccountRepository->findOrFail($id);

            if ($account->current_balance < $amount) {
                throw new \RuntimeException('Insufficient balance');
            }

            return $this->bankAccountRepository->updateBalance($id, $amount, 'subtract');
        });
    }

    public function getTotalBalance(?string $branchId = null): float
    {
        return $this->bankAccountRepository->getTotalBalance($branchId);
    }

    public function activate(string $id): mixed
    {
        return $this->bankAccountRepository->update($id, ['is_active' => true]);
    }

    public function deactivate(string $id): mixed
    {
        return $this->bankAccountRepository->update($id, ['is_active' => false]);
    }
}
