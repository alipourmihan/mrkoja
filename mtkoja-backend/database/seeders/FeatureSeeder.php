<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Feature;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            [
                'name' => 'پارکینگ رایگان',
                'slug' => 'free-parking',
                'icon' => '🅿️',
                'color' => '#10b981',
                'description' => 'پارکینگ رایگان برای مشتریان',
                'sort_order' => 1,
            ],
            [
                'name' => 'WiFi رایگان',
                'slug' => 'free-wifi',
                'icon' => '📶',
                'color' => '#3b82f6',
                'description' => 'دسترسی رایگان به اینترنت',
                'sort_order' => 2,
            ],
            [
                'name' => 'تحویل در محل',
                'slug' => 'delivery',
                'icon' => '🚚',
                'color' => '#f59e0b',
                'description' => 'ارسال و تحویل در محل',
                'sort_order' => 3,
            ],
            [
                'name' => 'پذیرش کارت',
                'slug' => 'card-payment',
                'icon' => '💳',
                'color' => '#8b5cf6',
                'description' => 'پذیرش پرداخت با کارت',
                'sort_order' => 4,
            ],
            [
                'name' => 'فضای کودک',
                'slug' => 'kids-area',
                'icon' => '👶',
                'color' => '#ec4899',
                'description' => 'فضای مخصوص کودکان',
                'sort_order' => 5,
            ],
            [
                'name' => 'دسترسی آسان',
                'slug' => 'wheelchair-accessible',
                'icon' => '♿',
                'color' => '#06b6d4',
                'description' => 'دسترسی آسان برای افراد کم‌توان',
                'sort_order' => 6,
            ],
            [
                'name' => 'سیگار ممنوع',
                'slug' => 'no-smoking',
                'icon' => '🚭',
                'color' => '#ef4444',
                'description' => 'محیط عاری از دود سیگار',
                'sort_order' => 7,
            ],
            [
                'name' => 'قیمت مناسب',
                'slug' => 'affordable',
                'icon' => '💰',
                'color' => '#10b981',
                'description' => 'قیمت‌های مناسب و مقرون به صرفه',
                'sort_order' => 8,
            ],
            [
                'name' => 'کیفیت بالا',
                'slug' => 'high-quality',
                'icon' => '⭐',
                'color' => '#f59e0b',
                'description' => 'کیفیت بالای خدمات و محصولات',
                'sort_order' => 9,
            ],
            [
                'name' => 'سرویس 24 ساعته',
                'slug' => '24-7-service',
                'icon' => '🕐',
                'color' => '#3b82f6',
                'description' => 'خدمات 24 ساعته',
                'sort_order' => 10,
            ],
        ];

        foreach ($features as $feature) {
            Feature::create($feature);
        }
    }
}