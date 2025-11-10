<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business;

class BusinessImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample image URLs for testing
        $sampleImages = [
            'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=400&h=300&fit=crop',
            'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=300&fit=crop',
            'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=400&h=300&fit=crop',
            'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=400&h=300&fit=crop',
            'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=400&h=300&fit=crop',
            'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=300&fit=crop',
        ];

        // Update all businesses with sample images
        Business::all()->each(function ($business) use ($sampleImages) {
            // Randomly select 1-3 images for each business
            $numImages = rand(1, 3);
            $selectedImages = array_slice($sampleImages, 0, $numImages);
            
            $business->update([
                'images' => $selectedImages
            ]);
        });

        $this->command->info('Sample images added to all businesses!');
    }
}