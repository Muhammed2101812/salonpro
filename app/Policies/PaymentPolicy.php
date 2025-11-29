<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Determine if the user can view any payments.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('payments.view');
    }

    /**
     * Determine if the user can view the payment.
     */
    public function view(User $user, Payment $payment): bool
    {
        // Super admin and organization admin can view all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Check branch through appointment or sale
        $branchId = null;
        if ($payment->appointment) {
            $branchId = $payment->appointment->branch_id;
        } elseif ($payment->sale && $payment->sale->employee) {
            $branchId = $payment->sale->employee->branch_id;
        }

        // Other users can only view payments in their branch
        return $user->can('payments.view') && $user->branch_id === $branchId;
    }

    /**
     * Determine if the user can create payments.
     */
    public function create(User $user): bool
    {
        return $user->can('payments.create');
    }

    /**
     * Determine if the user can update the payment.
     */
    public function update(User $user, Payment $payment): bool
    {
        // Super admin and organization admin can update all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Check branch through appointment or sale
        $branchId = null;
        if ($payment->appointment) {
            $branchId = $payment->appointment->branch_id;
        } elseif ($payment->sale && $payment->sale->employee) {
            $branchId = $payment->sale->employee->branch_id;
        }

        // Other users can only update payments in their branch
        return $user->can('payments.create') && $user->branch_id === $branchId;
    }

    /**
     * Determine if the user can delete the payment.
     */
    public function delete(User $user, Payment $payment): bool
    {
        // Super admin and organization admin can delete all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Check branch through appointment or sale
        $branchId = null;
        if ($payment->appointment) {
            $branchId = $payment->appointment->branch_id;
        } elseif ($payment->sale && $payment->sale->employee) {
            $branchId = $payment->sale->employee->branch_id;
        }

        // Other users can only delete payments in their branch
        return $user->can('payments.create') && $user->branch_id === $branchId;
    }

    /**
     * Determine if the user can restore the payment.
     */
    public function restore(User $user, Payment $payment): bool
    {
        return $this->delete($user, $payment);
    }

    /**
     * Determine if the user can permanently delete the payment.
     */
    public function forceDelete(User $user, Payment $payment): bool
    {
        // Only super admin can force delete
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine if the user can refund the payment.
     */
    public function refund(User $user, Payment $payment): bool
    {
        // Super admin and organization admin can refund all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Check branch through appointment or sale
        $branchId = null;
        if ($payment->appointment) {
            $branchId = $payment->appointment->branch_id;
        } elseif ($payment->sale && $payment->sale->employee) {
            $branchId = $payment->sale->employee->branch_id;
        }

        // Other users can only refund payments in their branch
        return $user->can('payments.refund') && $user->branch_id === $branchId;
    }

    /**
     * Determine if the user can view payment reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->can('payments.view-reports');
    }
}
