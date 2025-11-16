<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PurchaseOrderRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find purchase orders by branch.
     */
    public function findByBranch(string $branchId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Find purchase orders by supplier.
     */
    public function findBySupplier(string $supplierId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Find purchase orders by status.
     */
    public function findByStatus(string $status, ?string $branchId = null, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get pending purchase orders.
     */
    public function getPending(?string $branchId = null): Collection;

    /**
     * Get overdue purchase orders.
     */
    public function getOverdue(?string $branchId = null): Collection;

    /**
     * Generate next order number.
     */
    public function generateOrderNumber(): string;

    /**
     * Get purchase order totals by period.
     */
    public function getTotalsByPeriod(string $startDate, string $endDate, ?string $branchId = null): array;
}
