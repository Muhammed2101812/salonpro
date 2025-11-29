<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    /**
     * Determine if the user can view any invoices.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('payments.view');
    }

    /**
     * Determine if the user can view the invoice.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        // Super admin and organization admin can view all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only view invoices in their branch
        return $user->can('payments.view') && $user->branch_id === $invoice->branch_id;
    }

    /**
     * Determine if the user can create invoices.
     */
    public function create(User $user): bool
    {
        return $user->can('payments.create');
    }

    /**
     * Determine if the user can update the invoice.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        // Super admin and organization admin can update all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only update invoices in their branch
        return $user->can('payments.create') && $user->branch_id === $invoice->branch_id;
    }

    /**
     * Determine if the user can delete the invoice.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        // Super admin and organization admin can delete all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only delete invoices in their branch
        return $user->can('payments.create') && $user->branch_id === $invoice->branch_id;
    }

    /**
     * Determine if the user can restore the invoice.
     */
    public function restore(User $user, Invoice $invoice): bool
    {
        return $this->delete($user, $invoice);
    }

    /**
     * Determine if the user can permanently delete the invoice.
     */
    public function forceDelete(User $user, Invoice $invoice): bool
    {
        // Only super admin can force delete
        return $user->hasRole('Super Admin');
    }
}
