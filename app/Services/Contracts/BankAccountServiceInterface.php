<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface BankAccountServiceInterface extends BaseServiceInterface
{
    /**
     * Get bank accounts by branch.
     */
    public function getByBranch(string $branchId): mixed;

    /**
     * Get active bank accounts.
     */
    public function getActive(?string $branchId = null): mixed;

    /**
     * Deposit money.
     */
    public function deposit(string $id, float $amount): mixed;

    /**
     * Withdraw money.
     */
    public function withdraw(string $id, float $amount): mixed;

    /**
     * Get total balance.
     */
    public function getTotalBalance(?string $branchId = null): float;

    /**
     * Activate bank account.
     */
    public function activate(string $id): mixed;

    /**
     * Deactivate bank account.
     */
    public function deactivate(string $id): mixed;
}
