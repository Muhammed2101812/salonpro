<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class SettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('settings.view');
    }

    public function update(User $user): bool
    {
        return $user->can('settings.update');
    }

    public function updateSystem(User $user): bool
    {
        return $user->can('settings.system') && 
               $user->hasAnyRole(['Super Admin', 'Organization Admin']);
    }

    public function updateBranch(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Organization Admin', 'Branch Manager']);
    }
}
