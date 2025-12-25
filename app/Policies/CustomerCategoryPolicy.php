<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CustomerCategory;
use App\Models\User;

class CustomerCategoryPolicy
{
    /**
     * Determine if the user can view any customer categories.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Super Admin', 'Organization Admin', 'Branch Manager']) || $user->can('customers.view');
    }

    /**
     * Determine if the user can view the customer category.
     */
    public function view(User $user, CustomerCategory $customerCategory): bool
    {
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        return $user->can('customers.view') && $user->branch_id === $customerCategory->branch_id;
    }

    /**
     * Determine if the user can create customer categories.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['Super Admin', 'Organization Admin', 'Branch Manager']) || $user->can('customers.create');
    }

    /**
     * Determine if the user can update the customer category.
     */
    public function update(User $user, CustomerCategory $customerCategory): bool
    {
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        return $user->can('customers.update') && $user->branch_id === $customerCategory->branch_id;
    }

    /**
     * Determine if the user can delete the customer category.
     */
    public function delete(User $user, CustomerCategory $customerCategory): bool
    {
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        return $user->can('customers.delete') && $user->branch_id === $customerCategory->branch_id;
    }

    /**
     * Determine if the user can restore the customer category.
     */
    public function restore(User $user, CustomerCategory $customerCategory): bool
    {
        return $this->delete($user, $customerCategory);
    }

    /**
     * Determine if the user can permanently delete the customer category.
     */
    public function forceDelete(User $user, CustomerCategory $customerCategory): bool
    {
        return $user->hasRole('Super Admin');
    }
}
