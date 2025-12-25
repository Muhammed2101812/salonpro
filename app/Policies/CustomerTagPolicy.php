<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CustomerTag;
use App\Models\User;

class CustomerTagPolicy
{
    /**
     * Determine if the user can view any customer tags.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Super Admin', 'Organization Admin', 'Branch Manager']) || $user->can('customers.view');
    }

    /**
     * Determine if the user can view the customer tag.
     */
    public function view(User $user, CustomerTag $customerTag): bool
    {
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        return $user->can('customers.view') && $user->branch_id === $customerTag->branch_id;
    }

    /**
     * Determine if the user can create customer tags.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['Super Admin', 'Organization Admin', 'Branch Manager']) || $user->can('customers.create');
    }

    /**
     * Determine if the user can update the customer tag.
     */
    public function update(User $user, CustomerTag $customerTag): bool
    {
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        return $user->can('customers.update') && $user->branch_id === $customerTag->branch_id;
    }

    /**
     * Determine if the user can delete the customer tag.
     */
    public function delete(User $user, CustomerTag $customerTag): bool
    {
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        return $user->can('customers.delete') && $user->branch_id === $customerTag->branch_id;
    }

    /**
     * Determine if the user can restore the customer tag.
     */
    public function restore(User $user, CustomerTag $customerTag): bool
    {
        return $this->delete($user, $customerTag);
    }

    /**
     * Determine if the user can permanently delete the customer tag.
     */
    public function forceDelete(User $user, CustomerTag $customerTag): bool
    {
        return $user->hasRole('Super Admin');
    }
}
