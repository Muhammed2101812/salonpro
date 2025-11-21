<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface BankAccountRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find bank accounts by branch.
     */
    public function findByBranch(string $branchId): Collection;

    /**
     * Find active bank accounts.
     */
    public function findActive(?string $branchId = null): Collection;

    /**
     * Find by account number.
     */
    public function findByAccountNumber(string $accountNumber);

    /**
     * Find by IBAN.
     */
    public function findByIban(string $iban);

    /**
     * Update balance.
     */
    public function updateBalance(string $id, float $amount, string $operation = 'add');

    /**
     * Get total balance by branch.
     */
    public function getTotalBalance(?string $branchId = null): float;
}
