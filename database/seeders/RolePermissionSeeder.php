<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = $this->getPermissions();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $this->createRoles();
    }

    /**
     * Get all permissions.
     */
    private function getPermissions(): array
    {
        return [
            // Customer Management
            'customers.view',
            'customers.create',
            'customers.update',
            'customers.delete',
            'customers.export',

            // Employee Management
            'employees.view',
            'employees.create',
            'employees.update',
            'employees.delete',
            'employees.manage-schedule',
            'employees.view-performance',

            // Appointment Management
            'appointments.view',
            'appointments.create',
            'appointments.update',
            'appointments.delete',
            'appointments.cancel',
            'appointments.reschedule',
            'appointments.view-all', // View all appointments across branches

            // Service Management
            'services.view',
            'services.create',
            'services.update',
            'services.delete',
            'services.manage-categories',
            'services.manage-pricing',

            // Product Management
            'products.view',
            'products.create',
            'products.update',
            'products.delete',
            'products.manage-inventory',

            // Inventory Management
            'inventory.view',
            'inventory.create',
            'inventory.update',
            'inventory.delete',
            'inventory.transfer',
            'inventory.adjust',

            // Payment Management
            'payments.view',
            'payments.create',
            'payments.refund',
            'payments.view-reports',

            // Sales Management
            'sales.view',
            'sales.create',
            'sales.update',
            'sales.delete',

            // Expense Management
            'expenses.view',
            'expenses.create',
            'expenses.update',
            'expenses.delete',
            'expenses.approve',

            // Branch Management
            'branches.view',
            'branches.create',
            'branches.update',
            'branches.delete',
            'branches.manage-settings',

            // Report Access
            'reports.sales',
            'reports.financial',
            'reports.customer',
            'reports.employee',
            'reports.inventory',
            'reports.export',

            // User Management
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.manage-roles',
            'users.manage-permissions',

            // Settings Management
            'settings.view',
            'settings.update',
            'settings.system',

            // Audit Logs
            'audit.view',

            // Dashboard Access
            'dashboard.view',
            'dashboard.analytics',
        ];
    }

    /**
     * Create roles and assign permissions.
     */
    private function createRoles(): void
    {
        // 1. Super Admin - Full system access
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions(Permission::all());

        // 2. Organization Admin - Organization-wide access
        $orgAdmin = Role::firstOrCreate(['name' => 'Organization Admin']);
        $orgAdmin->syncPermissions([
            'customers.view', 'customers.create', 'customers.update', 'customers.delete', 'customers.export',
            'employees.view', 'employees.create', 'employees.update', 'employees.delete', 'employees.manage-schedule', 'employees.view-performance',
            'appointments.view', 'appointments.create', 'appointments.update', 'appointments.delete', 'appointments.cancel', 'appointments.reschedule', 'appointments.view-all',
            'services.view', 'services.create', 'services.update', 'services.delete', 'services.manage-categories', 'services.manage-pricing',
            'products.view', 'products.create', 'products.update', 'products.delete', 'products.manage-inventory',
            'inventory.view', 'inventory.create', 'inventory.update', 'inventory.delete', 'inventory.transfer', 'inventory.adjust',
            'payments.view', 'payments.create', 'payments.refund', 'payments.view-reports',
            'sales.view', 'sales.create', 'sales.update', 'sales.delete',
            'expenses.view', 'expenses.create', 'expenses.update', 'expenses.delete', 'expenses.approve',
            'branches.view', 'branches.create', 'branches.update', 'branches.manage-settings',
            'reports.sales', 'reports.financial', 'reports.customer', 'reports.employee', 'reports.inventory', 'reports.export',
            'users.view', 'users.create', 'users.update', 'users.delete', 'users.manage-roles',
            'settings.view', 'settings.update',
            'audit.view',
            'dashboard.view', 'dashboard.analytics',
        ]);

        // 3. Branch Manager - Branch-level management
        $branchManager = Role::firstOrCreate(['name' => 'Branch Manager']);
        $branchManager->syncPermissions([
            'customers.view', 'customers.create', 'customers.update', 'customers.export',
            'employees.view', 'employees.manage-schedule', 'employees.view-performance',
            'appointments.view', 'appointments.create', 'appointments.update', 'appointments.cancel', 'appointments.reschedule',
            'services.view', 'services.manage-pricing',
            'products.view', 'products.manage-inventory',
            'inventory.view', 'inventory.create', 'inventory.update', 'inventory.adjust',
            'payments.view', 'payments.create', 'payments.view-reports',
            'sales.view', 'sales.create',
            'expenses.view', 'expenses.create', 'expenses.update', 'expenses.approve',
            'reports.sales', 'reports.financial', 'reports.customer', 'reports.employee', 'reports.inventory',
            'users.view',
            'settings.view',
            'dashboard.view', 'dashboard.analytics',
        ]);

        // 4. Accountant - Financial access
        $accountant = Role::firstOrCreate(['name' => 'Accountant']);
        $accountant->syncPermissions([
            'customers.view',
            'appointments.view',
            'payments.view', 'payments.create', 'payments.refund', 'payments.view-reports',
            'sales.view',
            'expenses.view', 'expenses.create', 'expenses.update', 'expenses.approve',
            'reports.sales', 'reports.financial', 'reports.export',
            'dashboard.view',
        ]);

        // 5. Receptionist - Front desk operations
        $receptionist = Role::firstOrCreate(['name' => 'Receptionist']);
        $receptionist->syncPermissions([
            'customers.view', 'customers.create', 'customers.update',
            'appointments.view', 'appointments.create', 'appointments.update', 'appointments.cancel', 'appointments.reschedule',
            'services.view',
            'payments.view', 'payments.create',
            'sales.view', 'sales.create',
            'dashboard.view',
        ]);

        // 6. Stylist/Beautician - Service provider
        $stylist = Role::firstOrCreate(['name' => 'Stylist']);
        $stylist->syncPermissions([
            'customers.view',
            'appointments.view', 'appointments.update',
            'services.view',
            'products.view',
            'sales.view', 'sales.create',
            'dashboard.view',
        ]);

        // 7. Sales Representative
        $salesRep = Role::firstOrCreate(['name' => 'Sales Representative']);
        $salesRep->syncPermissions([
            'customers.view', 'customers.create', 'customers.update',
            'products.view',
            'sales.view', 'sales.create', 'sales.update',
            'payments.view', 'payments.create',
            'dashboard.view',
        ]);

        // 8. Inventory Manager
        $inventoryManager = Role::firstOrCreate(['name' => 'Inventory Manager']);
        $inventoryManager->syncPermissions([
            'products.view', 'products.create', 'products.update', 'products.manage-inventory',
            'inventory.view', 'inventory.create', 'inventory.update', 'inventory.transfer', 'inventory.adjust',
            'expenses.view',
            'reports.inventory', 'reports.export',
            'dashboard.view',
        ]);

        // 9. Marketing Manager
        $marketingManager = Role::firstOrCreate(['name' => 'Marketing Manager']);
        $marketingManager->syncPermissions([
            'customers.view', 'customers.export',
            'services.view',
            'reports.customer', 'reports.sales', 'reports.export',
            'dashboard.view', 'dashboard.analytics',
        ]);

        // 10. HR Manager
        $hrManager = Role::firstOrCreate(['name' => 'HR Manager']);
        $hrManager->syncPermissions([
            'employees.view', 'employees.create', 'employees.update', 'employees.manage-schedule', 'employees.view-performance',
            'users.view', 'users.create', 'users.update',
            'reports.employee',
            'dashboard.view',
        ]);

        // 11. Viewer - Read-only access
        $viewer = Role::firstOrCreate(['name' => 'Viewer']);
        $viewer->syncPermissions([
            'customers.view',
            'employees.view',
            'appointments.view',
            'services.view',
            'products.view',
            'inventory.view',
            'payments.view',
            'sales.view',
            'expenses.view',
            'dashboard.view',
        ]);
    }
}
