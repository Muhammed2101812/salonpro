<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first branch (Merkez Şube)
        $branch = Branch::where('code', 'MRK')->first();

        if (!$branch) {
            $this->command->error('Branch not found! Please run BranchSeeder first.');
            return;
        }

        // Create Super Admin user
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@salonpro.com',
            'password' => Hash::make('password'),
            'phone' => '+90 532 111 0001',
            'branch_id' => $branch->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Assign Super Admin role
        $admin->assignRole('Super Admin');

        $this->command->info('Super Admin user created successfully!');
        $this->command->info('Email: admin@salonpro.com');
        $this->command->info('Password: password');

        // Create Organization Admin user
        $orgAdmin = User::create([
            'name' => 'Organization Admin',
            'email' => 'org@salonpro.com',
            'password' => Hash::make('password'),
            'phone' => '+90 532 222 0002',
            'branch_id' => $branch->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $orgAdmin->assignRole('Organization Admin');

        // Create Branch Manager user
        $manager = User::create([
            'name' => 'Şube Müdürü',
            'email' => 'manager@salonpro.com',
            'password' => Hash::make('password'),
            'phone' => '+90 532 333 0003',
            'branch_id' => $branch->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $manager->assignRole('Branch Manager');

        // Create Receptionist user
        $receptionist = User::create([
            'name' => 'Resepsiyonist',
            'email' => 'reception@salonpro.com',
            'password' => Hash::make('password'),
            'phone' => '+90 532 444 0004',
            'branch_id' => $branch->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $receptionist->assignRole('Receptionist');

        $this->command->info('All test users created successfully!');
    }
}
