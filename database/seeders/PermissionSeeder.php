<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for each resource
        $resources = [
            'appointment',
            'branch',
            'customer',
            'employee',
            'product',
            'service',
            'sale',
            'payment',
            'expense',
            'invoice',
            'purchase-order',
            'stock-audit',
            'marketing-campaign',
            'loyalty-program',
            'system-setting',
        ];

        $actions = ['view', 'create', 'update', 'delete', 'restore', 'force-delete'];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                Permission::create(['name' => "{$action}-{$resource}"]);
            }
        }

        // Create roles and assign permissions
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all()->except([
            'force-delete-system-setting',
            'update-system-setting',
        ]));

        $manager = Role::create(['name' => 'manager']);
        $manager->givePermissionTo([
            'view-appointment', 'create-appointment', 'update-appointment', 'delete-appointment',
            'view-customer', 'create-customer', 'update-customer',
            'view-employee', 'create-employee', 'update-employee',
            'view-product', 'update-product',
            'view-service', 'update-service',
            'view-sale', 'create-sale',
            'view-payment', 'create-payment',
            'view-expense', 'create-expense',
            'view-invoice', 'create-invoice',
        ]);

        $employee = Role::create(['name' => 'employee']);
        $employee->givePermissionTo([
            'view-appointment', 'create-appointment', 'update-appointment',
            'view-customer', 'create-customer',
            'view-service',
            'view-product',
            'view-sale', 'create-sale',
        ]);

        $receptionist = Role::create(['name' => 'receptionist']);
        $receptionist->givePermissionTo([
            'view-appointment', 'create-appointment', 'update-appointment',
            'view-customer', 'create-customer', 'update-customer',
            'view-service',
            'view-employee',
            'view-payment', 'create-payment',
        ]);

        $this->command->info('✅ Permissions and roles created successfully!');
    }
}
