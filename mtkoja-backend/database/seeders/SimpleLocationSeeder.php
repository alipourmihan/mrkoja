<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\City;
use App\Models\Neighborhood;
use Illuminate\Support\Str;

class SimpleLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create provinces
        $provinces = [
            'تهران',
            'اصفهان', 
            'فارس',
            'خراسان رضوی',
            'آذربایجان شرقی',
        ];

        foreach ($provinces as $provinceName) {
            $province = Province::create([
                'name' => $provinceName,
                'slug' => Str::slug($provinceName),
                'is_active' => true,
                'sort_order' => 0,
            ]);

            // Create cities for each province
            $cities = $this->getCitiesForProvince($provinceName);
            foreach ($cities as $cityName) {
                $city = City::create([
                    'name' => $cityName,
                    'slug' => Str::slug($cityName),
                    'province_id' => $province->id,
                    'is_active' => true,
                    'sort_order' => 0,
                ]);

                // Create neighborhoods for each city
                $neighborhoods = $this->getNeighborhoodsForCity($cityName);
                foreach ($neighborhoods as $neighborhoodName) {
                    Neighborhood::create([
                        'name' => $neighborhoodName,
                        'slug' => Str::slug($neighborhoodName),
                        'city_id' => $city->id,
                        'is_active' => true,
                        'sort_order' => 0,
                    ]);
                }
            }
        }
    }

    private function getCitiesForProvince($provinceName)
    {
        $cities = [
            'تهران' => ['تهران', 'کرج', 'ورامین'],
            'اصفهان' => ['اصفهان', 'کاشان', 'نجف آباد'],
            'فارس' => ['شیراز', 'کازرون', 'مرودشت'],
            'خراسان رضوی' => ['مشهد', 'نیشابور', 'سبزوار'],
            'آذربایجان شرقی' => ['تبریز', 'مراغه', 'میانه'],
        ];

        return $cities[$provinceName] ?? ['مرکزی'];
    }

    private function getNeighborhoodsForCity($cityName)
    {
        $neighborhoods = [
            'تهران' => ['تجریش', 'ولیعصر', 'پاسداران'],
            'کرج' => ['مرکزی', 'مهرشهر', 'گوهردشت'],
            'اصفهان' => ['مرکزی', 'جلفا', 'شاهین شهر'],
            'شیراز' => ['مرکزی', 'زندیه', 'شهید بهشتی'],
            'مشهد' => ['مرکزی', 'احمدآباد', 'کوهسنگی'],
        ];

        return $neighborhoods[$cityName] ?? ['مرکزی'];
    }
}


