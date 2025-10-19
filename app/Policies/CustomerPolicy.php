<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('customers.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Customer $customer): bool
    {
        if (! $user->can('customers.view')) {
            return false;
        }

        // Branch isolation: User can only view customers from their branch
        return $this->checkBranchAccess($user, $customer);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('customers.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Customer $customer): bool
    {
        if (! $user->can('customers.update')) {
            return false;
        }

        return $this->checkBranchAccess($user, $customer);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Customer $customer): bool
    {
        if (! $user->can('customers.delete')) {
            return false;
        }

        return $this->checkBranchAccess($user, $customer);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Customer $customer): bool
    {
        if (! $user->can('customers.update')) {
            return false;
        }

        return $this->checkBranchAccess($user, $customer);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Customer $customer): bool
    {
        if (! $user->can('customers.delete')) {
            return false;
        }

        // Only Super Admin and Org Admin can force delete
        return $user->hasAnyRole(['Super Admin', 'Organization Admin']);
    }

    /**
     * Determine whether the user can export customer data.
     */
    public function export(User $user): bool
    {
        return $user->can('customers.export');
    }

    /**
     * Check if user has access to customer based on branch.
     */
    private function checkBranchAccess(User $user, Customer $customer): bool
    {
        // Super Admin and Org Admin have access to all branches
        if ($user->hasAnyRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only access their branch's customers
        return $user->branch_id === $customer->branch_id;
    }
}
