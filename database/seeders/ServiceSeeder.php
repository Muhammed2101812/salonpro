<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
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

        // 1. Saç Hizmetleri (Hair Services)
        $hairCategory = ServiceCategory::create([
            'branch_id' => $branch->id,
            'name' => 'Saç Hizmetleri',
            'description' => 'Profesyonel saç kesimi, boyama ve bakım hizmetleri',
            'color' => '#3B82F6',
            'icon' => 'scissors',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $hairServices = [
            [
                'name' => 'Erkek Saç Kesimi',
                'description' => 'Profesyonel erkek saç kesimi ve şekillendirme',
                'duration_minutes' => 30,
                'price' => 150.00,
            ],
            [
                'name' => 'Kadın Saç Kesimi',
                'description' => 'Profesyonel kadın saç kesimi ve şekillendirme',
                'duration_minutes' => 45,
                'price' => 250.00,
            ],
            [
                'name' => 'Saç Boyama',
                'description' => 'Profesyonel saç boyama hizmeti (tüm baş)',
                'duration_minutes' => 120,
                'price' => 500.00,
            ],
            [
                'name' => 'Keratin Bakımı',
                'description' => 'Keratin protein bakımı ile ipeksi saçlar',
                'duration_minutes' => 180,
                'price' => 800.00,
            ],
        ];

        foreach ($hairServices as $service) {
            Service::create([
                'branch_id' => $branch->id,
                'category_id' => $hairCategory->id,
                'name' => $service['name'],
                'description' => $service['description'],
                'duration_minutes' => $service['duration_minutes'],
                'price' => $service['price'],
                'cost' => $service['price'] * 0.3,
                'commission_rate' => 25.00,
                'is_active' => true,
                'requires_stylist' => true,
            ]);
        }

        // 2. Cilt Bakımı (Skin Care)
        $skinCategory = ServiceCategory::create([
            'branch_id' => $branch->id,
            'name' => 'Cilt Bakımı',
            'description' => 'Yüz ve cilt bakımı hizmetleri',
            'color' => '#10B981',
            'icon' => 'sparkles',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $skinServices = [
            [
                'name' => 'Klasik Cilt Bakımı',
                'description' => 'Temizleme, peeling, maske ve nemlendirme',
                'duration_minutes' => 60,
                'price' => 300.00,
            ],
            [
                'name' => 'Medikal Cilt Bakımı',
                'description' => 'Dermokozmetik ürünlerle profesyonel bakım',
                'duration_minutes' => 90,
                'price' => 500.00,
            ],
            [
                'name' => 'Altın Maske Bakımı',
                'description' => '24 ayar altın içeren özel maske bakımı',
                'duration_minutes' => 75,
                'price' => 600.00,
            ],
        ];

        foreach ($skinServices as $service) {
            Service::create([
                'branch_id' => $branch->id,
                'category_id' => $skinCategory->id,
                'name' => $service['name'],
                'description' => $service['description'],
                'duration_minutes' => $service['duration_minutes'],
                'price' => $service['price'],
                'cost' => $service['price'] * 0.35,
                'commission_rate' => 20.00,
                'is_active' => true,
                'requires_stylist' => true,
            ]);
        }

        // 3. Makyaj (Makeup)
        $makeupCategory = ServiceCategory::create([
            'branch_id' => $branch->id,
            'name' => 'Makyaj',
            'description' => 'Profesyonel makyaj hizmetleri',
            'color' => '#F59E0B',
            'icon' => 'palette',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        $makeupServices = [
            [
                'name' => 'Günlük Makyaj',
                'description' => 'Doğal ve şık günlük makyaj',
                'duration_minutes' => 30,
                'price' => 200.00,
            ],
            [
                'name' => 'Gece Makyajı',
                'description' => 'Özel günler için göz alıcı makyaj',
                'duration_minutes' => 45,
                'price' => 350.00,
            ],
            [
                'name' => 'Gelin Makyajı',
                'description' => 'Hayatınızın en özel günü için profesyonel makyaj',
                'duration_minutes' => 90,
                'price' => 1000.00,
            ],
        ];

        foreach ($makeupServices as $service) {
            Service::create([
                'branch_id' => $branch->id,
                'category_id' => $makeupCategory->id,
                'name' => $service['name'],
                'description' => $service['description'],
                'duration_minutes' => $service['duration_minutes'],
                'price' => $service['price'],
                'cost' => $service['price'] * 0.25,
                'commission_rate' => 30.00,
                'is_active' => true,
                'requires_stylist' => true,
            ]);
        }

        // 4. Tırnak Bakımı (Nail Care)
        $nailCategory = ServiceCategory::create([
            'branch_id' => $branch->id,
            'name' => 'Tırnak Bakımı',
            'description' => 'El ve ayak tırnak bakımı hizmetleri',
            'color' => '#EC4899',
            'icon' => 'hand',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        $nailServices = [
            [
                'name' => 'Manikür',
                'description' => 'Profesyonel el ve tırnak bakımı',
                'duration_minutes' => 45,
                'price' => 150.00,
            ],
            [
                'name' => 'Pedikür',
                'description' => 'Profesyonel ayak ve tırnak bakımı',
                'duration_minutes' => 60,
                'price' => 200.00,
            ],
            [
                'name' => 'Protez Tırnak',
                'description' => 'Kalıcı protez tırnak uygulaması',
                'duration_minutes' => 120,
                'price' => 400.00,
            ],
        ];

        foreach ($nailServices as $service) {
            Service::create([
                'branch_id' => $branch->id,
                'category_id' => $nailCategory->id,
                'name' => $service['name'],
                'description' => $service['description'],
                'duration_minutes' => $service['duration_minutes'],
                'price' => $service['price'],
                'cost' => $service['price'] * 0.3,
                'commission_rate' => 25.00,
                'is_active' => true,
                'requires_stylist' => true,
            ]);
        }

        // 5. Masaj (Massage)
        $massageCategory = ServiceCategory::create([
            'branch_id' => $branch->id,
            'name' => 'Masaj',
            'description' => 'Rahatlama ve terapi masajları',
            'color' => '#8B5CF6',
            'icon' => 'heart',
            'sort_order' => 5,
            'is_active' => true,
        ]);

        $massageServices = [
            [
                'name' => 'İsveç Masajı',
                'description' => 'Klasik rahatlatıcı masaj tekniği',
                'duration_minutes' => 60,
                'price' => 400.00,
            ],
            [
                'name' => 'Aromaterapi Masajı',
                'description' => 'Esansiyel yağlarla aromaterapi masajı',
                'duration_minutes' => 75,
                'price' => 500.00,
            ],
            [
                'name' => 'Sıcak Taş Masajı',
                'description' => 'Volkanik taşlarla terapi masajı',
                'duration_minutes' => 90,
                'price' => 600.00,
            ],
        ];

        foreach ($massageServices as $service) {
            Service::create([
                'branch_id' => $branch->id,
                'category_id' => $massageCategory->id,
                'name' => $service['name'],
                'description' => $service['description'],
                'duration_minutes' => $service['duration_minutes'],
                'price' => $service['price'],
                'cost' => $service['price'] * 0.2,
                'commission_rate' => 30.00,
                'is_active' => true,
                'requires_stylist' => true,
            ]);
        }

        $this->command->info('Service categories and services created successfully!');
        $this->command->info('Created 5 categories with 18 services total.');
    }
}
