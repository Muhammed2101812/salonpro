<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('payments.view');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if (! $user->can('payments.view')) {
            return false;
        }

        if ($user->hasAnyRole(['Super Admin', 'Organization Admin', 'Accountant'])) {
            return true;
        }

        return $user->branch_id === $invoice->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->can('payments.create');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        if (! $user->hasAnyRole(['Super Admin', 'Organization Admin', 'Accountant'])) {
            return false;
        }

        return $user->branch_id === $invoice->branch_id;
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Organization Admin']);
    }
}
