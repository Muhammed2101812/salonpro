<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;

class PurchaseOrderPolicy
{
    /**
     * Determine if the user can view any purchase orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('inventory.view');
    }

    /**
     * Determine if the user can view the purchase order.
     */
    public function view(User $user, PurchaseOrder $purchaseOrder): bool
    {
        // Super admin and organization admin can view all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only view purchase orders in their branch
        return $user->can('inventory.view') && $user->branch_id === $purchaseOrder->branch_id;
    }

    /**
     * Determine if the user can create purchase orders.
     */
    public function create(User $user): bool
    {
        return $user->can('inventory.create');
    }

    /**
     * Determine if the user can update the purchase order.
     */
    public function update(User $user, PurchaseOrder $purchaseOrder): bool
    {
        // Super admin and organization admin can update all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only update purchase orders in their branch
        return $user->can('inventory.update') && $user->branch_id === $purchaseOrder->branch_id;
    }

    /**
     * Determine if the user can delete the purchase order.
     */
    public function delete(User $user, PurchaseOrder $purchaseOrder): bool
    {
        // Super admin and organization admin can delete all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only delete purchase orders in their branch
        return $user->can('inventory.delete') && $user->branch_id === $purchaseOrder->branch_id;
    }

    /**
     * Determine if the user can restore the purchase order.
     */
    public function restore(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $this->delete($user, $purchaseOrder);
    }

    /**
     * Determine if the user can permanently delete the purchase order.
     */
    public function forceDelete(User $user, PurchaseOrder $purchaseOrder): bool
    {
        // Only super admin can force delete
        return $user->hasRole('Super Admin');
    }
}
