<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Neighborhood;
use App\Models\City;
use Illuminate\Support\Str;

class NeighborhoodSeeder extends Seeder
{
    public function run(): void
    {
        $neighborhoods = [
            // تهران
            ['city' => 'تهران', 'name' => 'ونک', 'name_en' => 'Vanak', 'latitude' => 35.7595, 'longitude' => 51.4108, 'sort_order' => 1],
            ['city' => 'تهران', 'name' => 'جردن', 'name_en' => 'Jordan', 'latitude' => 35.7555, 'longitude' => 51.4300, 'sort_order' => 2],
            ['city' => 'تهران', 'name' => 'زعفرانیه', 'name_en' => 'Zaferanieh', 'latitude' => 35.7981, 'longitude' => 51.4250, 'sort_order' => 3],
            ['city' => 'تهران', 'name' => 'پاسداران', 'name_en' => 'Pasdaran', 'latitude' => 35.7750, 'longitude' => 51.4500, 'sort_order' => 4],
            ['city' => 'تهران', 'name' => 'نیاوران', 'name_en' => 'Niavaran', 'latitude' => 35.8100, 'longitude' => 51.4300, 'sort_order' => 5],
            ['city' => 'تهران', 'name' => 'تجریش', 'name_en' => 'Tajrish', 'latitude' => 35.8000, 'longitude' => 51.4200, 'sort_order' => 6],
            ['city' => 'تهران', 'name' => 'شریعتی', 'name_en' => 'Shariati', 'latitude' => 35.7500, 'longitude' => 51.4400, 'sort_order' => 7],
            ['city' => 'تهران', 'name' => 'ولیعصر', 'name_en' => 'Valiasr', 'latitude' => 35.7200, 'longitude' => 51.4200, 'sort_order' => 8],
            ['city' => 'تهران', 'name' => 'انقلاب', 'name_en' => 'Enghelab', 'latitude' => 35.7000, 'longitude' => 51.4000, 'sort_order' => 9],
            ['city' => 'تهران', 'name' => 'کریمخان', 'name_en' => 'Karimkhan', 'latitude' => 35.6800, 'longitude' => 51.3800, 'sort_order' => 10],
            ['city' => 'تهران', 'name' => 'میرداماد', 'name_en' => 'Mirdamad', 'latitude' => 35.7600, 'longitude' => 51.4400, 'sort_order' => 11],
            ['city' => 'تهران', 'name' => 'سعادت آباد', 'name_en' => 'Saadatabad', 'latitude' => 35.7400, 'longitude' => 51.3600, 'sort_order' => 12],
            ['city' => 'تهران', 'name' => 'ستارخان', 'name_en' => 'Sattarkhan', 'latitude' => 35.7200, 'longitude' => 51.3600, 'sort_order' => 13],
            ['city' => 'تهران', 'name' => 'صادقیه', 'name_en' => 'Sadeghieh', 'latitude' => 35.7200, 'longitude' => 51.3400, 'sort_order' => 14],
            ['city' => 'تهران', 'name' => 'آزادی', 'name_en' => 'Azadi', 'latitude' => 35.7000, 'longitude' => 51.3200, 'sort_order' => 15],

            // اصفهان
            ['city' => 'اصفهان', 'name' => 'چهارباغ', 'name_en' => 'Chaharbagh', 'latitude' => 32.6500, 'longitude' => 51.6700, 'sort_order' => 1],
            ['city' => 'اصفهان', 'name' => 'سی و سه پل', 'name_en' => 'Si-o-se-pol', 'latitude' => 32.6400, 'longitude' => 51.6800, 'sort_order' => 2],
            ['city' => 'اصفهان', 'name' => 'نقش جهان', 'name_en' => 'Naghsh-e Jahan', 'latitude' => 32.6600, 'longitude' => 51.6800, 'sort_order' => 3],
            ['city' => 'اصفهان', 'name' => 'جلفا', 'name_en' => 'Jolfa', 'latitude' => 32.6300, 'longitude' => 51.6500, 'sort_order' => 4],

            // شیراز
            ['city' => 'شیراز', 'name' => 'زندیه', 'name_en' => 'Zandieh', 'latitude' => 29.6000, 'longitude' => 52.5800, 'sort_order' => 1],
            ['city' => 'شیراز', 'name' => 'شاهچراغ', 'name_en' => 'Shahcheragh', 'latitude' => 29.6100, 'longitude' => 52.5900, 'sort_order' => 2],
            ['city' => 'شیراز', 'name' => 'حافظیه', 'name_en' => 'Hafezieh', 'latitude' => 29.6200, 'longitude' => 52.6000, 'sort_order' => 3],
            ['city' => 'شیراز', 'name' => 'سعدیه', 'name_en' => 'Saadieh', 'latitude' => 29.6300, 'longitude' => 52.6100, 'sort_order' => 4],

            // مشهد
            ['city' => 'مشهد', 'name' => 'امام رضا', 'name_en' => 'Imam Reza', 'latitude' => 36.2800, 'longitude' => 59.6200, 'sort_order' => 1],
            ['city' => 'مشهد', 'name' => 'احمدآباد', 'name_en' => 'Ahmadabad', 'latitude' => 36.2600, 'longitude' => 59.6000, 'sort_order' => 2],
            ['city' => 'مشهد', 'name' => 'کوهسنگی', 'name_en' => 'Koohsangi', 'latitude' => 36.2700, 'longitude' => 59.6100, 'sort_order' => 3],

            // تبریز
            ['city' => 'تبریز', 'name' => 'شهید بهشتی', 'name_en' => 'Shahid Beheshti', 'latitude' => 38.0800, 'longitude' => 46.2900, 'sort_order' => 1],
            ['city' => 'تبریز', 'name' => 'باغمیشه', 'name_en' => 'Baghmesha', 'latitude' => 38.0900, 'longitude' => 46.3000, 'sort_order' => 2],

            // کرج
            ['city' => 'کرج', 'name' => 'مهرشهر', 'name_en' => 'Mehrshahr', 'latitude' => 35.8500, 'longitude' => 50.9400, 'sort_order' => 1],
            ['city' => 'کرج', 'name' => 'گلشهر', 'name_en' => 'Golshahr', 'latitude' => 35.8400, 'longitude' => 50.9300, 'sort_order' => 2],
            ['city' => 'کرج', 'name' => 'ماهدشت', 'name_en' => 'Mahdasht', 'latitude' => 35.8300, 'longitude' => 50.9200, 'sort_order' => 3],
        ];

        foreach ($neighborhoods as $neighborhoodData) {
            $city = City::where('name', $neighborhoodData['city'])->first();
            
            if ($city) {
                Neighborhood::create([
                    'city_id' => $city->id,
                    'name' => $neighborhoodData['name'],
                    'slug' => Str::slug($neighborhoodData['name']),
                    'name_en' => $neighborhoodData['name_en'],
                    'latitude' => $neighborhoodData['latitude'],
                    'longitude' => $neighborhoodData['longitude'],
                    'is_active' => true,
                    'sort_order' => $neighborhoodData['sort_order'],
                    'description' => "اطلاعات کامل درباره محله {$neighborhoodData['name']}",
                ]);
            }
        }
    }
}