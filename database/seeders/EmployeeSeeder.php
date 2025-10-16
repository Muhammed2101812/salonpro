<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();

        if ($branches->isEmpty()) {
            return;
        }

        $employees = [
            [
                'branch_id' => $branches->first()->id,
                'first_name' => 'Zeynep',
                'last_name' => 'Yılmaz',
                'phone' => '+90 532 100 2001',
                'email' => 'zeynep.yilmaz@salonpro.com',
                'specialties' => ['Saç Kesimi', 'Saç Boyama', 'Fön'],
                'commission_rate' => 15.00,
                'is_active' => true,
            ],
            [
                'branch_id' => $branches->first()->id,
                'first_name' => 'Ayşe',
                'last_name' => 'Kaya',
                'phone' => '+90 532 100 2002',
                'email' => 'ayse.kaya@salonpro.com',
                'specialties' => ['Manikür', 'Pedikür', 'Protez Tırnak'],
                'commission_rate' => 12.50,
                'is_active' => true,
            ],
            [
                'branch_id' => $branches->count() > 1 ? $branches->get(1)->id : $branches->first()->id,
                'first_name' => 'Mehmet',
                'last_name' => 'Demir',
                'phone' => '+90 532 100 2003',
                'email' => 'mehmet.demir@salonpro.com',
                'specialties' => ['Saç Kesimi', 'Sakal Tıraşı'],
                'commission_rate' => 10.00,
                'is_active' => true,
            ],
            [
                'branch_id' => $branches->count() > 1 ? $branches->get(1)->id : $branches->first()->id,
                'first_name' => 'Elif',
                'last_name' => 'Şahin',
                'phone' => '+90 532 100 2004',
                'email' => 'elif.sahin@salonpro.com',
                'specialties' => ['Kaş Tasarımı', 'Kirpik Lifting', 'Makyaj'],
                'commission_rate' => 13.00,
                'is_active' => true,
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
