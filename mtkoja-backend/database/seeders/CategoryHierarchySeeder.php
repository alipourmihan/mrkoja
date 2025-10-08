<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoryHierarchySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

        $education = Category::create([
            'name' => 'آموزش و تحصیل',
            'slug' => 'education',
            'description' => 'مدارس، دانشگاه‌ها و مراکز آموزشی',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        // Create child categories for Restaurant
        Category::create([
            'name' => 'رستوران ایرانی',
            'slug' => 'iranian-restaurant',
            'parent_id' => $restaurant->id,
            'description' => 'رستوران‌های سنتی ایرانی',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'name' => 'رستوران فرنگی',
            'slug' => 'foreign-restaurant',
            'parent_id' => $restaurant->id,
            'description' => 'رستوران‌های بین‌المللی',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Category::create([
            'name' => 'کافه و قهوه‌خانه',
            'slug' => 'cafe-coffee',
            'parent_id' => $restaurant->id,
            'description' => 'کافه‌ها و قهوه‌خانه‌ها',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        Category::create([
            'name' => 'فست فود',
            'slug' => 'fast-food',
            'parent_id' => $restaurant->id,
            'description' => 'فست فود و غذاهای آماده',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        // Create child categories for Shopping
        Category::create([
            'name' => 'پوشاک و مد',
            'slug' => 'fashion-clothing',
            'parent_id' => $shopping->id,
            'description' => 'فروشگاه‌های پوشاک و مد',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'name' => 'الکترونیک و دیجیتال',
            'slug' => 'electronics-digital',
            'parent_id' => $shopping->id,
            'description' => 'فروشگاه‌های لوازم الکترونیکی',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Category::create([
            'name' => 'خودرو و موتورسیکلت',
            'slug' => 'automotive',
            'parent_id' => $shopping->id,
            'description' => 'نمایشگاه‌ها و فروشگاه‌های خودرو',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Create child categories for Health
        Category::create([
            'name' => 'پزشک و درمان',
            'slug' => 'medical-treatment',
            'parent_id' => $health->id,
            'description' => 'مطب‌ها و کلینیک‌های پزشکی',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'name' => 'آرایشگاه و زیبایی',
            'slug' => 'beauty-salon',
            'parent_id' => $health->id,
            'description' => 'آرایشگاه‌ها و سالن‌های زیبایی',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Category::create([
            'name' => 'داروخانه',
            'slug' => 'pharmacy',
            'parent_id' => $health->id,
            'description' => 'داروخانه‌ها و مراکز دارویی',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Create child categories for Education
        Category::create([
            'name' => 'مدارس',
            'slug' => 'schools',
            'parent_id' => $education->id,
            'description' => 'مدارس ابتدایی، راهنمایی و دبیرستان',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'name' => 'دانشگاه‌ها',
            'slug' => 'universities',
            'parent_id' => $education->id,
            'description' => 'دانشگاه‌ها و مراکز آموزش عالی',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Category::create([
            'name' => 'آموزشگاه‌ها',
            'slug' => 'training-centers',
            'parent_id' => $education->id,
            'description' => 'آموزشگاه‌های آزاد و مراکز آموزشی',
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }
}
