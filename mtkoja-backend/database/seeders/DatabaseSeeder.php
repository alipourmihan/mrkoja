<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProvinceSeeder::class,
            CitySeeder::class,
            NeighborhoodSeeder::class,
            CategorySeeder::class,
            FeatureSeeder::class,
            ImageSeeder::class,
        ]);

        // حذف ساخت هرگونه کاربر پیش‌فرض طبق درخواست پروژه
    }
}
