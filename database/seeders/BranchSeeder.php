<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'name' => [
                    'tr' => 'Merkez Şube',
                    'en' => 'Main Branch',
                ],
                'code' => 'MRK',
                'phone' => '+90 212 555 0101',
                'email' => 'merkez@salonpro.com',
                'address' => 'Atatürk Mahallesi, İstiklal Caddesi No: 123',
                'city' => 'Istanbul',
                'country' => 'TR',
                'is_active' => true,
            ],
            [
                'name' => [
                    'tr' => 'Kadıköy Şubesi',
                    'en' => 'Kadikoy Branch',
                ],
                'code' => 'KDY',
                'phone' => '+90 216 555 0202',
                'email' => 'kadikoy@salonpro.com',
                'address' => 'Moda Mahallesi, Bahariye Caddesi No: 45',
                'city' => 'Istanbul',
                'country' => 'TR',
                'is_active' => true,
            ],
            [
                'name' => [
                    'tr' => 'Ankara Şubesi',
                    'en' => 'Ankara Branch',
                ],
                'code' => 'ANK',
                'phone' => '+90 312 555 0303',
                'email' => 'ankara@salonpro.com',
                'address' => 'Çankaya Mahallesi, Tunalı Hilmi Caddesi No: 67',
                'city' => 'Ankara',
                'country' => 'TR',
                'is_active' => true,
            ],
            [
                'name' => [
                    'tr' => 'İzmir Şubesi',
                    'en' => 'Izmir Branch',
                ],
                'code' => 'IZM',
                'phone' => '+90 232 555 0404',
                'email' => 'izmir@salonpro.com',
                'address' => 'Alsancak Mahallesi, Kıbrıs Şehitleri Caddesi No: 89',
                'city' => 'Izmir',
                'country' => 'TR',
                'is_active' => true,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
