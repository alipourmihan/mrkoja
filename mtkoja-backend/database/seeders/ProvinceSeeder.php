<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use Illuminate\Support\Str;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            ['name' => 'تهران', 'name_en' => 'Tehran', 'code' => '11', 'latitude' => 35.6892, 'longitude' => 51.3890, 'sort_order' => 1],
            ['name' => 'اصفهان', 'name_en' => 'Isfahan', 'code' => '04', 'latitude' => 32.6546, 'longitude' => 51.6680, 'sort_order' => 2],
            ['name' => 'فارس', 'name_en' => 'Fars', 'code' => '14', 'latitude' => 29.5918, 'longitude' => 52.5837, 'sort_order' => 3],
            ['name' => 'خراسان رضوی', 'name_en' => 'Khorasan Razavi', 'code' => '30', 'latitude' => 36.2605, 'longitude' => 59.6168, 'sort_order' => 4],
            ['name' => 'آذربایجان شرقی', 'name_en' => 'East Azerbaijan', 'code' => '01', 'latitude' => 38.0809, 'longitude' => 46.2919, 'sort_order' => 5],
            ['name' => 'مازندران', 'name_en' => 'Mazandaran', 'code' => '02', 'latitude' => 36.5659, 'longitude' => 53.0586, 'sort_order' => 6],
            ['name' => 'گیلان', 'name_en' => 'Gilan', 'code' => '07', 'latitude' => 37.2809, 'longitude' => 49.5921, 'sort_order' => 7],
            ['name' => 'کرمان', 'name_en' => 'Kerman', 'code' => '08', 'latitude' => 30.2839, 'longitude' => 57.0834, 'sort_order' => 8],
            ['name' => 'خوزستان', 'name_en' => 'Khuzestan', 'code' => '06', 'latitude' => 31.3183, 'longitude' => 48.6706, 'sort_order' => 9],
            ['name' => 'آذربایجان غربی', 'name_en' => 'West Azerbaijan', 'code' => '10', 'latitude' => 37.5522, 'longitude' => 45.0752, 'sort_order' => 10],
            ['name' => 'کرمانشاه', 'name_en' => 'Kermanshah', 'code' => '05', 'latitude' => 34.3142, 'longitude' => 47.0650, 'sort_order' => 11],
            ['name' => 'همدان', 'name_en' => 'Hamedan', 'code' => '13', 'latitude' => 34.7983, 'longitude' => 48.5150, 'sort_order' => 12],
            ['name' => 'یزد', 'name_en' => 'Yazd', 'code' => '21', 'latitude' => 31.8974, 'longitude' => 54.3568, 'sort_order' => 13],
            ['name' => 'اردبیل', 'name_en' => 'Ardabil', 'code' => '14', 'latitude' => 38.2541, 'longitude' => 48.2930, 'sort_order' => 14],
            ['name' => 'زنجان', 'name_en' => 'Zanjan', 'code' => '19', 'latitude' => 36.6769, 'longitude' => 48.4963, 'sort_order' => 15],
            ['name' => 'قزوین', 'name_en' => 'Qazvin', 'code' => '28', 'latitude' => 36.2688, 'longitude' => 50.0041, 'sort_order' => 16],
            ['name' => 'قم', 'name_en' => 'Qom', 'code' => '26', 'latitude' => 34.6399, 'longitude' => 50.8764, 'sort_order' => 17],
            ['name' => 'البرز', 'name_en' => 'Alborz', 'code' => '18', 'latitude' => 35.9960, 'longitude' => 50.9283, 'sort_order' => 18],
            ['name' => 'سیستان و بلوچستان', 'name_en' => 'Sistan and Baluchestan', 'code' => '16', 'latitude' => 29.4917, 'longitude' => 60.8636, 'sort_order' => 19],
            ['name' => 'کردستان', 'name_en' => 'Kurdistan', 'code' => '12', 'latitude' => 35.3020, 'longitude' => 47.0019, 'sort_order' => 20],
            ['name' => 'لرستان', 'name_en' => 'Lorestan', 'code' => '15', 'latitude' => 33.4878, 'longitude' => 48.3558, 'sort_order' => 21],
            ['name' => 'ایلام', 'name_en' => 'Ilam', 'code' => '22', 'latitude' => 33.6374, 'longitude' => 46.4227, 'sort_order' => 22],
            ['name' => 'کهگیلویه و بویراحمد', 'name_en' => 'Kohgiluyeh and Boyer-Ahmad', 'code' => '17', 'latitude' => 30.7238, 'longitude' => 50.8456, 'sort_order' => 23],
            ['name' => 'بوشهر', 'name_en' => 'Bushehr', 'code' => '24', 'latitude' => 28.9234, 'longitude' => 50.8203, 'sort_order' => 24],
            ['name' => 'هرمزگان', 'name_en' => 'Hormozgan', 'code' => '25', 'latitude' => 27.1865, 'longitude' => 56.2808, 'sort_order' => 25],
            ['name' => 'چهارمحال و بختیاری', 'name_en' => 'Chaharmahal and Bakhtiari', 'code' => '09', 'latitude' => 32.3266, 'longitude' => 50.8572, 'sort_order' => 26],
            ['name' => 'سمنان', 'name_en' => 'Semnan', 'code' => '20', 'latitude' => 35.5728, 'longitude' => 53.3971, 'sort_order' => 27],
            ['name' => 'گلستان', 'name_en' => 'Golestan', 'code' => '28', 'latitude' => 37.2891, 'longitude' => 55.1376, 'sort_order' => 28],
            ['name' => 'خراسان شمالی', 'name_en' => 'North Khorasan', 'code' => '29', 'latitude' => 37.4751, 'longitude' => 57.3313, 'sort_order' => 29],
            ['name' => 'خراسان جنوبی', 'name_en' => 'South Khorasan', 'code' => '30', 'latitude' => 32.8649, 'longitude' => 59.2262, 'sort_order' => 30],
            ['name' => 'مرکزی', 'name_en' => 'Markazi', 'code' => '00', 'latitude' => 34.0809, 'longitude' => 49.6913, 'sort_order' => 31],
        ];

        foreach ($provinces as $province) {
            Province::create([
                'name' => $province['name'],
                'slug' => Str::slug($province['name']),
                'name_en' => $province['name_en'],
                'code' => $province['code'],
                'latitude' => $province['latitude'],
                'longitude' => $province['longitude'],
                'is_active' => true,
                'sort_order' => $province['sort_order'],
                'description' => "اطلاعات کامل درباره استان {$province['name']}",
            ]);
        }
    }
}