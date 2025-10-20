<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\StockAudit;
use App\Models\User;

class StockAuditPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-stock-audit');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StockAudit $stockAudit): bool
    {
        return $user->hasPermissionTo('view-stock-audit');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-stock-audit');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StockAudit $stockAudit): bool
    {
        return $user->hasPermissionTo('update-stock-audit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StockAudit $stockAudit): bool
    {
        return $user->hasPermissionTo('delete-stock-audit');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StockAudit $stockAudit): bool
    {
        return $user->hasPermissionTo('restore-stock-audit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StockAudit $stockAudit): bool
    {
        return $user->hasPermissionTo('force-delete-stock-audit');
    }
}
