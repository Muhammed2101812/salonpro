<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface StockAlertServiceInterface extends BaseServiceInterface
{
    /**
     * Get stock alerts by branch.
     */
    public function getByBranch(string $branchId, int $perPage = 15): mixed;

    /**
     * Get stock alerts by product.
     */
    public function getByProduct(string $productId): mixed;

    /**
     * Get active stock alerts.
     */
    public function getActive(?string $branchId = null): mixed;

    /**
     * Get resolved stock alerts.
     */
    public function getResolved(?string $branchId = null): mixed;

    /**
     * Get critical alerts.
     */
    public function getCritical(?string $branchId = null): mixed;

    /**
     * Mark alert as notified.
     */
    public function markAsNotified(string $id): mixed;

    /**
     * Resolve alert.
     */
    public function resolve(string $id, ?string $notes = null): mixed;

    /**
     * Create alert from stock level check.
     */
    public function createFromStockCheck(array $data): mixed;
}
