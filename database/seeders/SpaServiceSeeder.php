<?php

namespace Database\Seeders;

use App\Models\SpaService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpaServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Skin Care', 'price' => 50000, 'image' => 'images/skincare.png', 'icon' => 'images/products.png',
                'description' => 'Revitalising facials and skincare treatments to leave you glowing and refreshed.'],
            ['name' => 'Manicure & Pedicure', 'price' => 70000, 'image' => 'images/manicure.png', 'icon' => 'images/treatments.png',
                'description' => 'Expert nail and hand care for beautifully groomed hands and feet.'],
            ['name' => 'Sauna Baths', 'price' => 50000, 'image' => 'images/sauna.jpg', 'icon' => 'images/ic_twotone-spa.png',
                'description' => 'Detoxify and unwind in our serene, heat-soothing sauna experience.'],
            ['name' => 'Massage', 'price' => 98000, 'image' => 'images/massage.png', 'icon' => 'images/temaki_spa.png',
                'description' => 'Therapeutic full-body massage to release tension and restore balance.'],
        ];

        foreach ($services as $i => $s) {
            SpaService::updateOrCreate(
                ['slug' => Str::slug($s['name'])],
                array_merge($s, ['is_active' => true, 'sort_order' => $i + 1]),
            );
        }
    }
}
