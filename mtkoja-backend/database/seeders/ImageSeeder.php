<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample image URLs for testing
        $sampleImages = [
            'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&h=600&fit=crop',
        ];

        // Get all businesses
        $businesses = Business::all();

        foreach ($businesses as $business) {
            // Randomly select 1-4 images for each business
            $numImages = rand(1, 4);
            $selectedImages = array_slice($sampleImages, 0, $numImages);
            
            foreach ($selectedImages as $index => $imageUrl) {
                // Create image record
                Image::create([
                    'imageable_id' => $business->id,
                    'imageable_type' => Business::class,
                    'filename' => 'sample_' . $business->id . '_' . $index . '.jpg',
                    'original_name' => 'sample_image_' . ($index + 1) . '.jpg',
                    'path' => 'businesses/' . $business->id . '/sample_' . $business->id . '_' . $index . '.jpg',
                    'mime_type' => 'image/jpeg',
                    'size' => rand(100000, 500000), // Random size between 100KB and 500KB
                    'is_primary' => $index === 0, // First image is primary
                    'sort_order' => $index,
                ]);
            }
        }

        $this->command->info('Sample images created for all businesses!');
    }
}
