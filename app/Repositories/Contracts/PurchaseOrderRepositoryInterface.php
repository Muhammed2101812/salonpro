<?php

namespace App\Repositories\Contracts;

use App\Models\PurchaseOrder;
use Illuminate\Pagination\LengthAwarePaginator;

interface PurchaseOrderRepositoryInterface
{
    /**
     * Get all purchase orders for the current branch with pagination
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all purchase orders for the current branch
     */
    public function getAll(): \Illuminate\Database\Eloquent\Collection;

    /**
     * Find a purchase order by ID
     */
    public function findById(int $id): ?PurchaseOrder;

    /**
     * Create a new purchase order
     */
    public function create(array $data): PurchaseOrder;

    /**
     * Update a purchase order
     */
    public function update(PurchaseOrder $purchaseOrder, array $data): bool;

    /**
     * Delete a purchase order
     */
    public function delete(PurchaseOrder $purchaseOrder): bool;

    /**
     * Get purchase orders by status
     */
    public function getByStatus(string $status): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get purchase orders by supplier
     */
    public function getBySupplier(int $supplierId): \Illuminate\Database\Eloquent\Collection;

    /**
     * Update purchase order status
     */
    public function updateStatus(PurchaseOrder $purchaseOrder, string $status): bool;

    /**
     * Get pending purchase orders
     */
    public function getPending(): \Illuminate\Database\Eloquent\Collection;
}
