<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Dashboard;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashboardPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the dashboard.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view the dashboard
        return true;
    }

    /**
     * Determine whether the user can view detailed dashboard stats.
     */
    public function view(User $user): bool
    {
        return true;
    }
}
