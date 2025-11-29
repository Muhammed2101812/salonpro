<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;

class BranchPolicy
{
    /**
     * Determine if the user can view any branches.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('branches.view');
    }

    /**
     * Determine if the user can view the branch.
     */
    public function view(User $user, Branch $branch): bool
    {
        // Super admin and organization admin can view all branches
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only view their own branch
        return $user->can('branches.view') && $user->branch_id === $branch->id;
    }

    /**
     * Determine if the user can create branches.
     */
    public function create(User $user): bool
    {
        // Only super admin and organization admin can create branches
        return $user->can('branches.create') && $user->hasRole(['Super Admin', 'Organization Admin']);
    }

    /**
     * Determine if the user can update the branch.
     */
    public function update(User $user, Branch $branch): bool
    {
        // Super admin and organization admin can update all branches
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return $user->can('branches.update');
        }

        // Branch managers can update their own branch
        return $user->can('branches.update') && $user->branch_id === $branch->id;
    }

    /**
     * Determine if the user can delete the branch.
     */
    public function delete(User $user, Branch $branch): bool
    {
        // Only super admin and organization admin can delete branches
        return $user->can('branches.delete') && $user->hasRole(['Super Admin', 'Organization Admin']);
    }

    /**
     * Determine if the user can restore the branch.
     */
    public function restore(User $user, Branch $branch): bool
    {
        return $this->delete($user, $branch);
    }

    /**
     * Determine if the user can permanently delete the branch.
     */
    public function forceDelete(User $user, Branch $branch): bool
    {
        // Only super admin can force delete
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine if the user can manage branch settings.
     */
    public function manageSettings(User $user, Branch $branch): bool
    {
        // Super admin and organization admin can manage all branch settings
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Branch managers can manage their own branch settings
        return $user->can('branches.manage-settings') && $user->branch_id === $branch->id;
    }

    /**
     * Determine if the user can view all branches.
     */
    public function viewAll(User $user): bool
    {
        return $user->hasRole(['Super Admin', 'Organization Admin']);
    }
}
