<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface CashRegisterServiceInterface extends BaseServiceInterface
{
    /**
     * Get cash registers by branch.
     */
    public function getByBranch(string $branchId): mixed;

    /**
     * Get active cash registers.
     */
    public function getActive(?string $branchId = null): mixed;

    /**
     * Add cash.
     */
    public function addCash(string $id, float $amount, ?string $note = null): mixed;

    /**
     * Remove cash.
     */
    public function removeCash(string $id, float $amount, ?string $note = null): mixed;

    /**
     * Open register.
     */
    public function openRegister(string $id, float $openingBalance): mixed;

    /**
     * Close register.
     */
    public function closeRegister(string $id, float $closingBalance): mixed;

    /**
     * Get total balance.
     */
    public function getTotalBalance(?string $branchId = null): float;
}
