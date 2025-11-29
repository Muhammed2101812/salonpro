<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    /**
     * Determine if the user can view any customers.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('customers.view');
    }

    /**
     * Determine if the user can view the customer.
     */
    public function view(User $user, Customer $customer): bool
    {
        // Super admin and organization admin can view all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only view customers in their branch
        return $user->can('customers.view') && $user->branch_id === $customer->branch_id;
    }

    /**
     * Determine if the user can create customers.
     */
    public function create(User $user): bool
    {
        return $user->can('customers.create');
    }

    /**
     * Determine if the user can update the customer.
     */
    public function update(User $user, Customer $customer): bool
    {
        // Super admin and organization admin can update all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only update customers in their branch
        return $user->can('customers.update') && $user->branch_id === $customer->branch_id;
    }

    /**
     * Determine if the user can delete the customer.
     */
    public function delete(User $user, Customer $customer): bool
    {
        // Super admin and organization admin can delete all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only delete customers in their branch
        return $user->can('customers.delete') && $user->branch_id === $customer->branch_id;
    }

    /**
     * Determine if the user can restore the customer.
     */
    public function restore(User $user, Customer $customer): bool
    {
        return $this->delete($user, $customer);
    }

    /**
     * Determine if the user can permanently delete the customer.
     */
    public function forceDelete(User $user, Customer $customer): bool
    {
        // Only super admin can force delete
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine if the user can export customers.
     */
    public function export(User $user): bool
    {
        return $user->can('customers.export');
    }

    /**
     * Determine if the user can view customers across all branches.
     */
    public function viewAll(User $user): bool
    {
        return $user->hasRole(['Super Admin', 'Organization Admin', 'Marketing Manager']);
    }
}
