<?php

namespace App\Services;

use App\Models\Business;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BehtarinoScraperService
{
    private string $apiBaseUrl;
    
    public function __construct()
    {
        $this->apiBaseUrl = config('scraper.api_url', 'http://localhost:8000');
    }
    
    /**
     * Get businesses from the scraper API
     */
    public function getBusinesses(array $filters = []): array
    {
        try {
            $response = Http::timeout(30)->get($this->apiBaseUrl . '/businesses', $filters);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            Log::error('Scraper API error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return [];
            
        } catch (\Exception $e) {
            Log::error('Scraper API connection failed', [
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }
    
    /**
     * Sync businesses from scraper to local database
     */
    public function syncBusinesses(array $filters = []): int
    {
        $businesses = $this->getBusinesses($filters);
        
        if (empty($businesses['data'])) {
            return 0;
        }
        
        $synced = 0;
        
        foreach ($businesses['data'] as $businessData) {
            try {
                Business::updateOrCreate(
                    ['slug_or_link' => $businessData['slug_or_link']],
                    [
                        'title' => $businessData['title'],
                        'address' => $businessData['address'],
                        'phone' => $businessData['phone'],
                        'category' => $businessData['category'],
                        'rating' => $businessData['rating'],
                        'reviews_count' => $businessData['reviews_count'],
                        'scraped_at' => $businessData['scraped_at'],
                    ]
                );
                
                $synced++;
                
            } catch (\Exception $e) {
                Log::error('Failed to sync business', [
                    'business' => $businessData['title'],
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $synced;
    }
    
    /**
     * Get businesses by category
     */
    public function getBusinessesByCategory(string $category, int $page = 1, int $perPage = 10): array
    {
        return $this->getBusinesses([
            'category' => $category,
            'page' => $page,
            'per_page' => $perPage
        ]);
    }
    
    /**
     * Search businesses
     */
    public function searchBusinesses(string $query, int $page = 1, int $perPage = 10): array
    {
        return $this->getBusinesses([
            'search' => $query,
            'page' => $page,
            'per_page' => $perPage
        ]);
    }
}
