<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'رستوران',
                'slug' => 'restaurant',
                'description' => 'رستوران‌ها و کافه‌ها',
                'is_active' => true,
                'sort_order' => 1,
                'icon' => '🍽️',
                'color' => '#FF6B6B',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'فروشگاه',
                'slug' => 'store',
                'description' => 'فروشگاه‌ها و مغازه‌ها',
                'is_active' => true,
                'sort_order' => 2,
                'icon' => '🛍️',
                'color' => '#4ECDC4',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'خدمات درمانی',
                'slug' => 'healthcare',
                'description' => 'بیمارستان‌ها، کلینیک‌ها و داروخانه‌ها',
                'is_active' => true,
                'sort_order' => 3,
                'icon' => '🏥',
                'color' => '#45B7D1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'آموزش',
                'slug' => 'education',
                'description' => 'مدارس، دانشگاه‌ها و آموزشگاه‌ها',
                'is_active' => true,
                'sort_order' => 4,
                'icon' => '🎓',
                'color' => '#96CEB4',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'ورزش و تفریح',
                'slug' => 'sports',
                'description' => 'باشگاه‌های ورزشی و مراکز تفریحی',
                'is_active' => true,
                'sort_order' => 5,
                'icon' => '⚽',
                'color' => '#FFEAA7',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('categories')->whereIn('slug', ['restaurant', 'store', 'healthcare', 'education', 'sports'])->delete();
    }
};
