<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface StockAlertRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get stock alerts by branch.
     */
    public function findByBranch(string $branchId, int $perPage = 15): mixed;

    /**
     * Get stock alerts by product.
     */
    public function findByProduct(string $productId): Collection;

    /**
     * Get active (unresolved) stock alerts.
     */
    public function getActive(?string $branchId = null): Collection;

    /**
     * Get resolved stock alerts.
     */
    public function getResolved(?string $branchId = null): Collection;

    /**
     * Get alerts by type.
     */
    public function getByType(string $alertType, ?string $branchId = null): Collection;

    /**
     * Get alerts by priority.
     */
    public function getByPriority(int $priority, ?string $branchId = null): Collection;

    /**
     * Mark alert as notified.
     */
    public function markAsNotified(string $id): mixed;

    /**
     * Mark alert as resolved.
     */
    public function markAsResolved(string $id, ?string $notes = null): mixed;

    /**
     * Get critical alerts (high priority unresolved).
     */
    public function getCriticalAlerts(?string $branchId = null): Collection;
}
