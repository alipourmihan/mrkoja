<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class SimpleCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing categories
        Category::truncate();

        // Create parent categories
        $restaurant = Category::create([
            'name' => 'رستوران و کافه',
            'slug' => 'restaurant-cafe',
            'description' => 'رستوران‌ها، کافه‌ها و مراکز غذایی',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $shopping = Category::create([
            'name' => 'خرید و فروش',
            'slug' => 'shopping',
            'description' => 'فروشگاه‌ها و مراکز خرید',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $health = Category::create([
            'name' => 'سلامت و زیبایی',
            'slug' => 'health-beauty',
            'description' => 'مراکز درمانی، آرایشگاه‌ها و سالن‌های زیبایی',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Create child categories
        Category::create([
            'name' => 'رستوران ایرانی',
            'slug' => 'iranian-restaurant',
            'parent_id' => $restaurant->id,
            'description' => 'رستوران‌های سنتی ایرانی',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'name' => 'کافه و قهوه‌خانه',
            'slug' => 'cafe-coffee',
            'parent_id' => $restaurant->id,
            'description' => 'کافه‌ها و قهوه‌خانه‌ها',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Category::create([
            'name' => 'پوشاک',
            'slug' => 'clothing',
            'parent_id' => $shopping->id,
            'description' => 'فروشگاه‌های پوشاک',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'name' => 'الکترونیک',
            'slug' => 'electronics',
            'parent_id' => $shopping->id,
            'description' => 'فروشگاه‌های لوازم الکترونیک',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Category::create([
            'name' => 'آرایشگاه',
            'slug' => 'beauty-salon',
            'parent_id' => $health->id,
            'description' => 'آرایشگاه‌های زنانه و مردانه',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'name' => 'داروخانه',
            'slug' => 'pharmacy',
            'parent_id' => $health->id,
            'description' => 'داروخانه‌ها و مراکز دارویی',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        echo "Categories created successfully!\n";
    }
}



