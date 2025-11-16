<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface PurchaseOrderServiceInterface extends BaseServiceInterface
{
    /**
     * Get purchase orders by branch.
     */
    public function getByBranch(string $branchId, int $perPage = 15): mixed;

    /**
     * Get purchase orders by supplier.
     */
    public function getBySupplier(string $supplierId, int $perPage = 15): mixed;

    /**
     * Get pending purchase orders.
     */
    public function getPending(?string $branchId = null): mixed;

    /**
     * Get overdue purchase orders.
     */
    public function getOverdue(?string $branchId = null): mixed;

    /**
     * Create purchase order with items.
     */
    public function createWithItems(array $data): mixed;

    /**
     * Update purchase order status.
     */
    public function updateStatus(string $id, string $status): mixed;

    /**
     * Receive purchase order.
     */
    public function receive(string $id, array $data): mixed;

    /**
     * Cancel purchase order.
     */
    public function cancel(string $id, ?string $reason = null): mixed;

    /**
     * Get totals by period.
     */
    public function getTotalsByPeriod(string $startDate, string $endDate, ?string $branchId = null): array;
}
