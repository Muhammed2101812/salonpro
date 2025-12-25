<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CustomerSegment;
use App\Models\User;

class CustomerSegmentPolicy
{
    /**
     * Determine if the user can view any customer segments.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Super Admin', 'Organization Admin', 'Branch Manager']) || $user->can('customers.view');
    }

    /**
     * Determine if the user can view the customer segment.
     */
    public function view(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->hasRole(['Super Admin', 'Organization Admin', 'Branch Manager']) || $user->can('customers.view');
    }

    /**
     * Determine if the user can create customer segments.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['Super Admin', 'Organization Admin', 'Branch Manager']) || $user->can('customers.create');
    }

    /**
     * Determine if the user can update the customer segment.
     */
    public function update(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->hasRole(['Super Admin', 'Organization Admin', 'Branch Manager']) || $user->can('customers.update');
    }

    /**
     * Determine if the user can delete the customer segment.
     */
    public function delete(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->hasRole(['Super Admin', 'Organization Admin', 'Branch Manager']) || $user->can('customers.delete');
    }

    /**
     * Determine if the user can restore the customer segment.
     */
    public function restore(User $user, CustomerSegment $customerSegment): bool
    {
        return $this->delete($user, $customerSegment);
    }

    /**
     * Determine if the user can permanently delete the customer segment.
     */
    public function forceDelete(User $user, CustomerSegment $customerSegment): bool
    {
        return $user->hasRole('Super Admin');
    }
}
