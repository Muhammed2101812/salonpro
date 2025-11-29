<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    /**
     * Determine if the user can view any employees.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('employees.view');
    }

    /**
     * Determine if the user can view the employee.
     */
    public function view(User $user, Employee $employee): bool
    {
        // Super admin and organization admin can view all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only view employees in their branch
        return $user->can('employees.view') && $user->branch_id === $employee->branch_id;
    }

    /**
     * Determine if the user can create employees.
     */
    public function create(User $user): bool
    {
        return $user->can('employees.create');
    }

    /**
     * Determine if the user can update the employee.
     */
    public function update(User $user, Employee $employee): bool
    {
        // Super admin and organization admin can update all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only update employees in their branch
        return $user->can('employees.update') && $user->branch_id === $employee->branch_id;
    }

    /**
     * Determine if the user can delete the employee.
     */
    public function delete(User $user, Employee $employee): bool
    {
        // Super admin and organization admin can delete all
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only delete employees in their branch
        return $user->can('employees.delete') && $user->branch_id === $employee->branch_id;
    }

    /**
     * Determine if the user can restore the employee.
     */
    public function restore(User $user, Employee $employee): bool
    {
        return $this->delete($user, $employee);
    }

    /**
     * Determine if the user can permanently delete the employee.
     */
    public function forceDelete(User $user, Employee $employee): bool
    {
        // Only super admin can force delete
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine if the user can manage employee schedules.
     */
    public function manageSchedule(User $user, Employee $employee): bool
    {
        // Super admin and organization admin can manage all schedules
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only manage schedules for employees in their branch
        return $user->can('employees.manage-schedule') && $user->branch_id === $employee->branch_id;
    }

    /**
     * Determine if the user can view employee performance.
     */
    public function viewPerformance(User $user, Employee $employee): bool
    {
        // Super admin and organization admin can view all performance
        if ($user->hasRole(['Super Admin', 'Organization Admin'])) {
            return true;
        }

        // Other users can only view performance for employees in their branch
        return $user->can('employees.view-performance') && $user->branch_id === $employee->branch_id;
    }
}
