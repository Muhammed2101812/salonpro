<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();

        if ($branches->isEmpty()) {
            return;
        }

        $customers = [
            [
                'branch_id' => $branches->first()->id,
                'first_name' => 'Ahmet',
                'last_name' => 'Yılmaz',
                'phone' => '+90 532 111 2233',
                'email' => 'ahmet.yilmaz@example.com',
                'date_of_birth' => '1990-05-15',
                'gender' => 'male',
                'address' => 'İstanbul, Kadıköy',
                'notes' => 'Düzenli müşteri',
                'is_active' => true,
            ],
            [
                'branch_id' => $branches->first()->id,
                'first_name' => 'Ayşe',
                'last_name' => 'Demir',
                'phone' => '+90 532 444 5566',
                'email' => 'ayse.demir@example.com',
                'date_of_birth' => '1985-08-22',
                'gender' => 'female',
                'address' => 'İstanbul, Beşiktaş',
                'notes' => 'Kaş yaptırma tercih eder',
                'is_active' => true,
            ],
            [
                'branch_id' => $branches->count() > 1 ? $branches->get(1)->id : $branches->first()->id,
                'first_name' => 'Mehmet',
                'last_name' => 'Kaya',
                'phone' => '+90 532 777 8899',
                'email' => 'mehmet.kaya@example.com',
                'date_of_birth' => '1995-12-10',
                'gender' => 'male',
                'address' => 'Ankara, Çankaya',
                'is_active' => true,
            ],
            [
                'branch_id' => $branches->count() > 1 ? $branches->get(1)->id : $branches->first()->id,
                'first_name' => 'Fatma',
                'last_name' => 'Şahin',
                'phone' => '+90 532 333 1122',
                'email' => 'fatma.sahin@example.com',
                'date_of_birth' => '1992-03-18',
                'gender' => 'female',
                'address' => 'İzmir, Alsancak',
                'notes' => 'Boya ve kesim',
                'is_active' => true,
            ],
            [
                'branch_id' => $branches->count() > 2 ? $branches->get(2)->id : $branches->first()->id,
                'first_name' => 'Can',
                'last_name' => 'Öztürk',
                'phone' => '+90 532 555 6677',
                'email' => null,
                'date_of_birth' => '1988-07-25',
                'gender' => 'male',
                'address' => 'Ankara, Kızılay',
                'is_active' => true,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
