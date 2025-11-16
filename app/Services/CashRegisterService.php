<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CashRegisterRepositoryInterface;
use App\Services\Contracts\CashRegisterServiceInterface;
use Illuminate\Support\Facades\DB;

class CashRegisterService extends BaseService implements CashRegisterServiceInterface
{
    public function __construct(
        protected CashRegisterRepositoryInterface $cashRegisterRepository
    ) {
        parent::__construct($cashRegisterRepository);
    }

    public function getByBranch(string $branchId): mixed
    {
        return $this->cashRegisterRepository->findByBranch($branchId);
    }

    public function getActive(?string $branchId = null): mixed
    {
        return $this->cashRegisterRepository->findActive($branchId);
    }

    public function addCash(string $id, float $amount, ?string $note = null): mixed
    {
        return DB::transaction(function () use ($id, $amount, $note) {
            if ($amount <= 0) {
                throw new \InvalidArgumentException('Amount must be greater than zero');
            }

            return $this->cashRegisterRepository->updateBalance($id, $amount, 'add');
        });
    }

    public function removeCash(string $id, float $amount, ?string $note = null): mixed
    {
        return DB::transaction(function () use ($id, $amount, $note) {
            if ($amount <= 0) {
                throw new \InvalidArgumentException('Amount must be greater than zero');
            }

            $register = $this->cashRegisterRepository->findOrFail($id);

            if ($register->current_balance < $amount) {
                throw new \RuntimeException('Insufficient balance in cash register');
            }

            return $this->cashRegisterRepository->updateBalance($id, $amount, 'subtract');
        });
    }

    public function openRegister(string $id, float $openingBalance): mixed
    {
        return DB::transaction(function () use ($id, $openingBalance) {
            return $this->cashRegisterRepository->open($id, $openingBalance);
        });
    }

    public function closeRegister(string $id, float $closingBalance): mixed
    {
        return DB::transaction(function () use ($id, $closingBalance) {
            return $this->cashRegisterRepository->close($id, $closingBalance);
        });
    }

    public function getTotalBalance(?string $branchId = null): float
    {
        return $this->cashRegisterRepository->getTotalBalance($branchId);
    }
}
