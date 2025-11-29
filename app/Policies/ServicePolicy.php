<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    /**
     * Determine if the user can view any services.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('services.view');
    }

    /**
     * Determine if the user can view the service.
     */
    public function view(User $user, Service $service): bool
    {
        return $user->can('services.view');
    }

    /**
     * Determine if the user can create services.
     */
    public function create(User $user): bool
    {
        return $user->can('services.create');
    }

    /**
     * Determine if the user can update the service.
     */
    public function update(User $user, Service $service): bool
    {
        return $user->can('services.update');
    }

    /**
     * Determine if the user can delete the service.
     */
    public function delete(User $user, Service $service): bool
    {
        return $user->can('services.delete');
    }

    /**
     * Determine if the user can restore the service.
     */
    public function restore(User $user, Service $service): bool
    {
        return $this->delete($user, $service);
    }

    /**
     * Determine if the user can permanently delete the service.
     */
    public function forceDelete(User $user, Service $service): bool
    {
        // Only super admin can force delete
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine if the user can manage service categories.
     */
    public function manageCategories(User $user): bool
    {
        return $user->can('services.manage-categories');
    }

    /**
     * Determine if the user can manage service pricing.
     */
    public function managePricing(User $user): bool
    {
        return $user->can('services.manage-pricing');
    }
}
