<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\City;
use App\Models\Neighborhood;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        Neighborhood::truncate();
        City::truncate();
        Province::truncate();

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
            foreach ($cities as $cityData) {
                $city = City::create([
                    'name' => $cityData,
                    'slug' => Str::slug($cityData),
                    'province_id' => $province->id,
                    'is_active' => true,
                    'sort_order' => 0,
                ]);

                // Create neighborhoods for each city
                $neighborhoods = $this->getNeighborhoodsForCity($cityData);
                foreach ($neighborhoods as $neighborhoodData) {
                    Neighborhood::create([
                        'name' => $neighborhoodData,
                        'slug' => Str::slug($neighborhoodData),
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
            'تهران' => ['تهران', 'کرج', 'ورامین', 'شهریار'],
            'اصفهان' => ['اصفهان', 'کاشان', 'نجف آباد', 'خمینی شهر'],
            'فارس' => ['شیراز', 'کازرون', 'مرودشت', 'جهرم'],
            'خراسان رضوی' => ['مشهد', 'نیشابور', 'سبزوار', 'تربت حیدریه'],
            'آذربایجان شرقی' => ['تبریز', 'مراغه', 'میانه', 'اهر'],
        ];

        return $cities[$provinceName] ?? [];
    }

    private function getNeighborhoodsForCity($cityName)
    {
        $neighborhoods = [
            'تهران' => ['تجریش', 'ولیعصر', 'پاسداران', 'جردن', 'ونک'],
            'کرج' => ['مرکزی', 'مهرشهر', 'گوهردشت', 'ماهدشت'],
            'اصفهان' => ['مرکزی', 'جلفا', 'شاهین شهر', 'خمینی شهر'],
            'شیراز' => ['مرکزی', 'زندیه', 'شهید بهشتی', 'ملاصدرا'],
            'مشهد' => ['مرکزی', 'احمدآباد', 'کوهسنگی', 'طوس'],
        ];

        return $neighborhoods[$cityName] ?? ['مرکزی'];
    }
}
