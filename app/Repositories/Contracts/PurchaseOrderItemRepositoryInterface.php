<?php

namespace App\Repositories\Contracts;

use App\Models\PurchaseOrderItem;

interface PurchaseOrderItemRepositoryInterface
{
    /**
     * Get all items for a purchase order
     */
    public function getByPurchaseOrder(int $purchaseOrderId): \Illuminate\Database\Eloquent\Collection;

    /**
     * Find a purchase order item by ID
     */
    public function findById(int $id): ?PurchaseOrderItem;

    /**
     * Create a new purchase order item
     */
    public function create(array $data): PurchaseOrderItem;

    /**
     * Update a purchase order item
     */
    public function update(PurchaseOrderItem $item, array $data): bool;

    /**
     * Delete a purchase order item
     */
    public function delete(PurchaseOrderItem $item): bool;

    /**
     * Create multiple items for a purchase order
     */
    public function createMultiple(int $purchaseOrderId, array $items): array;

    /**
     * Update received quantity
     */
    public function updateReceivedQuantity(PurchaseOrderItem $item, int $quantity): bool;
}
