<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface CashRegisterRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find cash registers by branch.
     */
    public function findByBranch(string $branchId): Collection;

    /**
     * Find active cash registers.
     */
    public function findActive(?string $branchId = null): Collection;

    /**
     * Update balance.
     */
    public function updateBalance(string $id, float $amount, string $operation = 'add');

    /**
     * Get total balance by branch.
     */
    public function getTotalBalance(?string $branchId = null): float;

    /**
     * Open cash register.
     */
    public function open(string $id, float $openingBalance);

    /**
     * Close cash register.
     */
    public function close(string $id, float $closingBalance);
}
