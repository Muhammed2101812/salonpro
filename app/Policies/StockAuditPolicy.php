<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\StockAudit;
use App\Models\User;

class StockAuditPolicy
{
    /**
     * Determine if the user can view any stock audits.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('inventory.view');
    }

    /**
     * Determine if the user can view the stock audit.
     */
    public function view(User $user, StockAudit $stockAudit): bool
    {
        // Super admin and organization admin can view all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only view stock audits in their branch
        return $user->can('inventory.view') && $user->branch_id === $stockAudit->branch_id;
    }

    /**
     * Determine if the user can create stock audits.
     */
    public function create(User $user): bool
    {
        return $user->can('inventory.create');
    }

    /**
     * Determine if the user can update the stock audit.
     */
    public function update(User $user, StockAudit $stockAudit): bool
    {
        // Super admin and organization admin can update all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only update stock audits in their branch
        return $user->can('inventory.update') && $user->branch_id === $stockAudit->branch_id;
    }

    /**
     * Determine if the user can delete the stock audit.
     */
    public function delete(User $user, StockAudit $stockAudit): bool
    {
        // Super admin and organization admin can delete all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only delete stock audits in their branch
        return $user->can('inventory.delete') && $user->branch_id === $stockAudit->branch_id;
    }

    /**
     * Determine if the user can restore the stock audit.
     */
    public function restore(User $user, StockAudit $stockAudit): bool
    {
        return $this->delete($user, $stockAudit);
    }

    /**
     * Determine if the user can permanently delete the stock audit.
     */
    public function forceDelete(User $user, StockAudit $stockAudit): bool
    {
        // Only super admin can force delete
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine if the user can approve the stock audit.
     */
    public function approve(User $user, StockAudit $stockAudit): bool
    {
        // Super admin and organization admin can approve all stock audits
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Branch managers can approve stock audits in their branch
        return $user->hasRole('Branch Manager') && $user->branch_id === $stockAudit->branch_id;
    }
}
