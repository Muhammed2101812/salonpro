<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    /**
     * Determine if the user can view any expenses.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('expenses.view');
    }

    /**
     * Determine if the user can view the expense.
     */
    public function view(User $user, Expense $expense): bool
    {
        // Super admin and organization admin can view all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only view expenses in their branch
        return $user->can('expenses.view') && $user->branch_id === $expense->branch_id;
    }

    /**
     * Determine if the user can create expenses.
     */
    public function create(User $user): bool
    {
        return $user->can('expenses.create');
    }

    /**
     * Determine if the user can update the expense.
     */
    public function update(User $user, Expense $expense): bool
    {
        // Super admin and organization admin can update all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only update expenses in their branch
        return $user->can('expenses.update') && $user->branch_id === $expense->branch_id;
    }

    /**
     * Determine if the user can delete the expense.
     */
    public function delete(User $user, Expense $expense): bool
    {
        // Super admin and organization admin can delete all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only delete expenses in their branch
        return $user->can('expenses.delete') && $user->branch_id === $expense->branch_id;
    }

    /**
     * Determine if the user can restore the expense.
     */
    public function restore(User $user, Expense $expense): bool
    {
        return $this->delete($user, $expense);
    }

    /**
     * Determine if the user can permanently delete the expense.
     */
    public function forceDelete(User $user, Expense $expense): bool
    {
        // Only super admin can force delete
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine if the user can approve the expense.
     */
    public function approve(User $user, Expense $expense): bool
    {
        // Super admin and organization admin can approve all expenses
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only approve expenses in their branch
        return $user->can('expenses.approve') && $user->branch_id === $expense->branch_id;
    }
}
