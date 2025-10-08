<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'رستوران',
                'slug' => 'restaurant',
                'description' => 'رستوران‌ها و کافه‌ها',
                'is_active' => true,
                'sort_order' => 1,
                'icon' => '🍽️',
                'color' => '#FF6B6B'
            ],
            [
                'name' => 'فروشگاه',
                'slug' => 'store',
                'description' => 'فروشگاه‌ها و مغازه‌ها',
                'is_active' => true,
                'sort_order' => 2,
                'icon' => '🛍️',
                'color' => '#4ECDC4'
            ],
            [
                'name' => 'خدمات درمانی',
                'slug' => 'healthcare',
                'description' => 'بیمارستان‌ها، کلینیک‌ها و داروخانه‌ها',
                'is_active' => true,
                'sort_order' => 3,
                'icon' => '🏥',
                'color' => '#45B7D1'
            ],
            [
                'name' => 'آموزش',
                'slug' => 'education',
                'description' => 'مدارس، دانشگاه‌ها و آموزشگاه‌ها',
                'is_active' => true,
                'sort_order' => 4,
                'icon' => '🎓',
                'color' => '#96CEB4'
            ],
            [
                'name' => 'ورزش و تفریح',
                'slug' => 'sports',
                'description' => 'باشگاه‌های ورزشی و مراکز تفریحی',
                'is_active' => true,
                'sort_order' => 5,
                'icon' => '⚽',
                'color' => '#FFEAA7'
            ],
            [
                'name' => 'خودرو',
                'slug' => 'automotive',
                'description' => 'نمایشگاه‌های خودرو و تعمیرگاه‌ها',
                'is_active' => true,
                'sort_order' => 6,
                'icon' => '🚗',
                'color' => '#DDA0DD'
            ],
            [
                'name' => 'زیبایی و آرایش',
                'slug' => 'beauty',
                'description' => 'آرایشگاه‌ها، سالن‌های زیبایی و اسپا',
                'is_active' => true,
                'sort_order' => 7,
                'icon' => '💄',
                'color' => '#FFB6C1'
            ],
            [
                'name' => 'سفر و گردشگری',
                'slug' => 'travel',
                'description' => 'هتل‌ها، آژانس‌های مسافرتی و تورها',
                'is_active' => true,
                'sort_order' => 8,
                'icon' => '✈️',
                'color' => '#87CEEB'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}