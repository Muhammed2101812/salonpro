<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\InventoryMovement;
use App\Models\User;

class InventoryPolicy
{
    /**
     * Determine if the user can view any inventory movements.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('inventory.view');
    }

    /**
     * Determine if the user can view the inventory movement.
     */
    public function view(User $user, InventoryMovement $inventoryMovement): bool
    {
        return $user->can('inventory.view');
    }

    /**
     * Determine if the user can create inventory movements.
     */
    public function create(User $user): bool
    {
        return $user->can('inventory.create');
    }

    /**
     * Determine if the user can update inventory movements.
     */
    public function update(User $user, InventoryMovement $inventoryMovement): bool
    {
        return $user->can('inventory.update');
    }

    /**
     * Determine if the user can delete inventory movements.
     */
    public function delete(User $user, InventoryMovement $inventoryMovement): bool
    {
        return $user->can('inventory.delete');
    }

    /**
     * Determine if the user can restore the inventory movement.
     */
    public function restore(User $user, InventoryMovement $inventoryMovement): bool
    {
        return $this->delete($user, $inventoryMovement);
    }

    /**
     * Determine if the user can permanently delete the inventory movement.
     */
    public function forceDelete(User $user, InventoryMovement $inventoryMovement): bool
    {
        // Only super admin can force delete
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine if the user can transfer inventory between branches.
     */
    public function transfer(User $user): bool
    {
        return $user->can('inventory.transfer');
    }

    /**
     * Determine if the user can adjust inventory quantities.
     */
    public function adjust(User $user): bool
    {
        return $user->can('inventory.adjust');
    }
}
