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

        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@mtkoja.com',
            'password' => \Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create business owner user
        User::create([
            'name' => 'Business Owner',
            'email' => 'owner@mtkoja.com',
            'password' => \Hash::make('password'),
            'role' => 'business_owner',
        ]);
    }
}
