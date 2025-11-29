<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    /**
     * Determine if the user can view any appointments.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('appointments.view');
    }

    /**
     * Determine if the user can view the appointment.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        // Users with view-all permission can view all appointments
        if ($user->can('appointments.view-all')) {
            return true;
        }

        // Other users can only view appointments in their branch
        return $user->can('appointments.view') && $user->branch_id === $appointment->branch_id;
    }

    /**
     * Determine if the user can create appointments.
     */
    public function create(User $user): bool
    {
        return $user->can('appointments.create');
    }

    /**
     * Determine if the user can update the appointment.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        // Users with view-all permission can update all appointments
        if ($user->can('appointments.view-all')) {
            return true;
        }

        // Other users can only update appointments in their branch
        return $user->can('appointments.update') && $user->branch_id === $appointment->branch_id;
    }

    /**
     * Determine if the user can delete the appointment.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        // Users with view-all permission can delete all appointments
        if ($user->can('appointments.view-all')) {
            return true;
        }

        // Other users can only delete appointments in their branch
        return $user->can('appointments.delete') && $user->branch_id === $appointment->branch_id;
    }

    /**
     * Determine if the user can cancel the appointment.
     */
    public function cancel(User $user, Appointment $appointment): bool
    {
        // Users with view-all permission can cancel all appointments
        if ($user->can('appointments.view-all')) {
            return true;
        }

        // Other users can only cancel appointments in their branch
        return $user->can('appointments.cancel') && $user->branch_id === $appointment->branch_id;
    }

    /**
     * Determine if the user can reschedule the appointment.
     */
    public function reschedule(User $user, Appointment $appointment): bool
    {
        // Users with view-all permission can reschedule all appointments
        if ($user->can('appointments.view-all')) {
            return true;
        }

        // Other users can only reschedule appointments in their branch
        return $user->can('appointments.reschedule') && $user->branch_id === $appointment->branch_id;
    }

    /**
     * Determine if the user can restore the appointment.
     */
    public function restore(User $user, Appointment $appointment): bool
    {
        return $this->delete($user, $appointment);
    }

    /**
     * Determine if the user can permanently delete the appointment.
     */
    public function forceDelete(User $user, Appointment $appointment): bool
    {
        // Only super admin can force delete
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine if the user can view appointments across all branches.
     */
    public function viewAll(User $user): bool
    {
        return $user->can('appointments.view-all');
    }
}
