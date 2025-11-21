<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\CashRegister;
use App\Repositories\Contracts\CashRegisterRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CashRegisterRepository extends BaseRepository implements CashRegisterRepositoryInterface
{
    public function __construct(CashRegister $model)
    {
        parent::__construct($model);
    }

    public function findByBranch(string $branchId): Collection
    {
        return $this->model->where('branch_id', $branchId)
            ->with('branch')
            ->orderBy('name')
            ->get();
    }

    public function findActive(?string $branchId = null): Collection
    {
        $query = $this->model->where('is_active', true)
            ->with('branch');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('name')->get();
    }

    public function updateBalance(string $id, float $amount, string $operation = 'add')
    {
        $register = $this->findOrFail($id);

        $newBalance = $operation === 'add'
            ? $register->current_balance + $amount
            : $register->current_balance - $amount;

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

    public function open(string $id, float $openingBalance)
    {
        return $this->update($id, [
            'opening_balance' => $openingBalance,
            'current_balance' => $openingBalance,
            'is_active' => true,
        ]);
    }

    public function close(string $id, float $closingBalance)
    {
        return $this->update($id, [
            'current_balance' => $closingBalance,
            'is_active' => false,
        ]);
    }
}
