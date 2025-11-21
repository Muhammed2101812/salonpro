<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface SupplierServiceInterface extends BaseServiceInterface
{
    /**
     * Get active suppliers.
     */
    public function getActive(): mixed;

    /**
     * Search suppliers.
     */
    public function search(string $query, int $perPage = 15): mixed;

    /**
     * Get supplier statistics.
     */
    public function getSupplierStats(string $supplierId): array;

    /**
     * Activate supplier.
     */
    public function activate(string $id): mixed;

    /**
     * Deactivate supplier.
     */
    public function deactivate(string $id): mixed;
}
