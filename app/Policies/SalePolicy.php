<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    /**
     * Determine if the user can view any sales.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('sales.view');
    }

    /**
     * Determine if the user can view the sale.
     */
    public function view(User $user, Sale $sale): bool
    {
        // Super admin and organization admin can view all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only view sales in their branch
        $branchId = $sale->employee?->branch_id;
        return $user->can('sales.view') && $user->branch_id === $branchId;
    }

    /**
     * Determine if the user can create sales.
     */
    public function create(User $user): bool
    {
        return $user->can('sales.create');
    }

    /**
     * Determine if the user can update the sale.
     */
    public function update(User $user, Sale $sale): bool
    {
        // Super admin and organization admin can update all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only update sales in their branch
        $branchId = $sale->employee?->branch_id;
        return $user->can('sales.update') && $user->branch_id === $branchId;
    }

    /**
     * Determine if the user can delete the sale.
     */
    public function delete(User $user, Sale $sale): bool
    {
        // Super admin and organization admin can delete all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only delete sales in their branch
        $branchId = $sale->employee?->branch_id;
        return $user->can('sales.delete') && $user->branch_id === $branchId;
    }

    /**
     * Determine if the user can restore the sale.
     */
    public function restore(User $user, Sale $sale): bool
    {
        return $this->delete($user, $sale);
    }

    /**
     * Determine if the user can permanently delete the sale.
     */
    public function forceDelete(User $user, Sale $sale): bool
    {
        // Only super admin can force delete
        return $user->hasRole('Super Admin');
    }
}
