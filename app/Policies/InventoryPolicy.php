<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class InventoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('inventory.view');
    }

    public function create(User $user): bool
    {
        return $user->can('inventory.create');
    }

    public function update(User $user): bool
    {
        return $user->can('inventory.update');
    }

    public function delete(User $user): bool
    {
        return $user->can('inventory.delete');
    }

    public function transfer(User $user): bool
    {
        return $user->can('inventory.transfer');
    }

    public function adjust(User $user): bool
    {
        return $user->can('inventory.adjust');
    }
}
