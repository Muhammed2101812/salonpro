<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('sales.view');
    }

    public function view(User $user, Sale $sale): bool
    {
        if (! $user->can('sales.view')) {
            return false;
        }

        if ($user->hasAnyRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        return $user->branch_id === $sale->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->can('sales.create');
    }

    public function update(User $user, Sale $sale): bool
    {
        if (! $user->can('sales.update')) {
            return false;
        }

        if ($user->hasAnyRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        return $user->branch_id === $sale->branch_id;
    }

    public function delete(User $user, Sale $sale): bool
    {
        if (! $user->can('sales.delete')) {
            return false;
        }

        if ($user->hasAnyRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        return $user->branch_id === $sale->branch_id;
    }
}
