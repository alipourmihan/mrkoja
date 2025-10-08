<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Province;
use Illuminate\Support\Str;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            // تهران
            ['province' => 'تهران', 'name' => 'تهران', 'name_en' => 'Tehran', 'code' => '001', 'latitude' => 35.6892, 'longitude' => 51.3890, 'sort_order' => 1],
            ['province' => 'تهران', 'name' => 'کرج', 'name_en' => 'Karaj', 'code' => '002', 'latitude' => 35.8400, 'longitude' => 50.9391, 'sort_order' => 2],
            ['province' => 'تهران', 'name' => 'ورامین', 'name_en' => 'Varamin', 'code' => '003', 'latitude' => 35.3252, 'longitude' => 51.6472, 'sort_order' => 3],
            ['province' => 'تهران', 'name' => 'شهریار', 'name_en' => 'Shahriar', 'code' => '004', 'latitude' => 35.6597, 'longitude' => 51.0592, 'sort_order' => 4],
            ['province' => 'تهران', 'name' => 'ملارد', 'name_en' => 'Malard', 'code' => '005', 'latitude' => 35.6658, 'longitude' => 50.9767, 'sort_order' => 5],

            // اصفهان
            ['province' => 'اصفهان', 'name' => 'اصفهان', 'name_en' => 'Isfahan', 'code' => '101', 'latitude' => 32.6546, 'longitude' => 51.6680, 'sort_order' => 1],
            ['province' => 'اصفهان', 'name' => 'کاشان', 'name_en' => 'Kashan', 'code' => '102', 'latitude' => 33.9850, 'longitude' => 51.4100, 'sort_order' => 2],
            ['province' => 'اصفهان', 'name' => 'نجف آباد', 'name_en' => 'Najafabad', 'code' => '103', 'latitude' => 32.6333, 'longitude' => 51.3667, 'sort_order' => 3],
            ['province' => 'اصفهان', 'name' => 'خمینی شهر', 'name_en' => 'Khomeini Shahr', 'code' => '104', 'latitude' => 32.7000, 'longitude' => 51.5214, 'sort_order' => 4],

            // فارس
            ['province' => 'فارس', 'name' => 'شیراز', 'name_en' => 'Shiraz', 'code' => '201', 'latitude' => 29.5918, 'longitude' => 52.5837, 'sort_order' => 1],
            ['province' => 'فارس', 'name' => 'کازرون', 'name_en' => 'Kazerun', 'code' => '202', 'latitude' => 29.6194, 'longitude' => 51.6544, 'sort_order' => 2],
            ['province' => 'فارس', 'name' => 'مرودشت', 'name_en' => 'Marvdasht', 'code' => '203', 'latitude' => 29.8742, 'longitude' => 52.8025, 'sort_order' => 3],

            // خراسان رضوی
            ['province' => 'خراسان رضوی', 'name' => 'مشهد', 'name_en' => 'Mashhad', 'code' => '301', 'latitude' => 36.2605, 'longitude' => 59.6168, 'sort_order' => 1],
            ['province' => 'خراسان رضوی', 'name' => 'نیشابور', 'name_en' => 'Neyshabur', 'code' => '302', 'latitude' => 36.2140, 'longitude' => 58.7961, 'sort_order' => 2],
            ['province' => 'خراسان رضوی', 'name' => 'سبزوار', 'name_en' => 'Sabzevar', 'code' => '303', 'latitude' => 36.2125, 'longitude' => 57.6819, 'sort_order' => 3],

            // آذربایجان شرقی
            ['province' => 'آذربایجان شرقی', 'name' => 'تبریز', 'name_en' => 'Tabriz', 'code' => '401', 'latitude' => 38.0809, 'longitude' => 46.2919, 'sort_order' => 1],
            ['province' => 'آذربایجان شرقی', 'name' => 'مراغه', 'name_en' => 'Maragheh', 'code' => '402', 'latitude' => 37.3891, 'longitude' => 46.2375, 'sort_order' => 2],

            // مازندران
            ['province' => 'مازندران', 'name' => 'ساری', 'name_en' => 'Sari', 'code' => '501', 'latitude' => 36.5659, 'longitude' => 53.0586, 'sort_order' => 1],
            ['province' => 'مازندران', 'name' => 'بابل', 'name_en' => 'Babol', 'code' => '502', 'latitude' => 36.5445, 'longitude' => 52.6789, 'sort_order' => 2],
            ['province' => 'مازندران', 'name' => 'آمل', 'name_en' => 'Amol', 'code' => '503', 'latitude' => 36.4696, 'longitude' => 52.3507, 'sort_order' => 3],

            // گیلان
            ['province' => 'گیلان', 'name' => 'رشت', 'name_en' => 'Rasht', 'code' => '601', 'latitude' => 37.2809, 'longitude' => 49.5921, 'sort_order' => 1],
            ['province' => 'گیلان', 'name' => 'انزلی', 'name_en' => 'Anzali', 'code' => '602', 'latitude' => 37.4728, 'longitude' => 49.4622, 'sort_order' => 2],

            // کرمان
            ['province' => 'کرمان', 'name' => 'کرمان', 'name_en' => 'Kerman', 'code' => '701', 'latitude' => 30.2839, 'longitude' => 57.0834, 'sort_order' => 1],
            ['province' => 'کرمان', 'name' => 'رفسنجان', 'name_en' => 'Rafsanjan', 'code' => '702', 'latitude' => 30.4067, 'longitude' => 55.9939, 'sort_order' => 2],

            // خوزستان
            ['province' => 'خوزستان', 'name' => 'اهواز', 'name_en' => 'Ahvaz', 'code' => '801', 'latitude' => 31.3183, 'longitude' => 48.6706, 'sort_order' => 1],
            ['province' => 'خوزستان', 'name' => 'آبادان', 'name_en' => 'Abadan', 'code' => '802', 'latitude' => 30.3392, 'longitude' => 48.3043, 'sort_order' => 2],
            ['province' => 'خوزستان', 'name' => 'خرمشهر', 'name_en' => 'Khorramshahr', 'code' => '803', 'latitude' => 30.4256, 'longitude' => 48.1894, 'sort_order' => 3],

            // آذربایجان غربی
            ['province' => 'آذربایجان غربی', 'name' => 'ارومیه', 'name_en' => 'Urmia', 'code' => '901', 'latitude' => 37.5522, 'longitude' => 45.0752, 'sort_order' => 1],
            ['province' => 'آذربایجان غربی', 'name' => 'خوی', 'name_en' => 'Khoy', 'code' => '902', 'latitude' => 38.5503, 'longitude' => 44.9521, 'sort_order' => 2],

            // کرمانشاه
            ['province' => 'کرمانشاه', 'name' => 'کرمانشاه', 'name_en' => 'Kermanshah', 'code' => '1001', 'latitude' => 34.3142, 'longitude' => 47.0650, 'sort_order' => 1],

            // همدان
            ['province' => 'همدان', 'name' => 'همدان', 'name_en' => 'Hamedan', 'code' => '1101', 'latitude' => 34.7983, 'longitude' => 48.5150, 'sort_order' => 1],

            // یزد
            ['province' => 'یزد', 'name' => 'یزد', 'name_en' => 'Yazd', 'code' => '1201', 'latitude' => 31.8974, 'longitude' => 54.3568, 'sort_order' => 1],

            // اردبیل
            ['province' => 'اردبیل', 'name' => 'اردبیل', 'name_en' => 'Ardabil', 'code' => '1301', 'latitude' => 38.2541, 'longitude' => 48.2930, 'sort_order' => 1],

            // زنجان
            ['province' => 'زنجان', 'name' => 'زنجان', 'name_en' => 'Zanjan', 'code' => '1401', 'latitude' => 36.6769, 'longitude' => 48.4963, 'sort_order' => 1],

            // قزوین
            ['province' => 'قزوین', 'name' => 'قزوین', 'name_en' => 'Qazvin', 'code' => '1501', 'latitude' => 36.2688, 'longitude' => 50.0041, 'sort_order' => 1],

            // قم
            ['province' => 'قم', 'name' => 'قم', 'name_en' => 'Qom', 'code' => '1601', 'latitude' => 34.6399, 'longitude' => 50.8764, 'sort_order' => 1],

            // البرز
            ['province' => 'البرز', 'name' => 'کرج', 'name_en' => 'Karaj', 'code' => '1701', 'latitude' => 35.8400, 'longitude' => 50.9391, 'sort_order' => 1],
        ];

        foreach ($cities as $cityData) {
            $province = Province::where('name', $cityData['province'])->first();
            
            if ($province) {
                City::create([
                    'province_id' => $province->id,
                    'name' => $cityData['name'],
                    'slug' => Str::slug($cityData['name']),
                    'name_en' => $cityData['name_en'],
                    'code' => $cityData['code'],
                    'latitude' => $cityData['latitude'],
                    'longitude' => $cityData['longitude'],
                    'is_active' => true,
                    'sort_order' => $cityData['sort_order'],
                    'description' => "اطلاعات کامل درباره شهر {$cityData['name']}",
                ]);
            }
        }
    }
}