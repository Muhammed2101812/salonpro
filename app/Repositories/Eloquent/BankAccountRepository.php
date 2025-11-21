<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\BankAccount;
use App\Repositories\Contracts\BankAccountRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BankAccountRepository extends BaseRepository implements BankAccountRepositoryInterface
{
    public function __construct(BankAccount $model)
    {
        parent::__construct($model);
    }

    public function findByBranch(string $branchId): Collection
    {
        return $this->model->where('branch_id', $branchId)
            ->with('branch')
            ->orderBy('bank_name')
            ->get();
    }

    public function findActive(?string $branchId = null): Collection
    {
        $query = $this->model->where('is_active', true)
            ->with('branch');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('bank_name')->get();
    }

    public function findByAccountNumber(string $accountNumber)
    {
        return $this->model->where('account_number', $accountNumber)->first();
    }

    public function findByIban(string $iban)
    {
        return $this->model->where('iban', $iban)->first();
    }

    public function updateBalance(string $id, float $amount, string $operation = 'add')
    {
        $account = $this->findOrFail($id);

        $newBalance = $operation === 'add'
            ? $account->current_balance + $amount
            : $account->current_balance - $amount;

        return $this->update($id, ['current_balance' => $newBalance]);
    }

    public function getTotalBalance(?string $branchId = null): float
    {
        $query = $this->model->where('is_active', true);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return (float) $query->sum('current_balance');
    }
}
