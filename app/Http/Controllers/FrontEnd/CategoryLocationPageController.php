<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\MiscellaneousController;
use App\Models\CategoryLocationPage;
use App\Models\Listing\ListingContent;
use App\Models\Listing\Listing;
use App\Models\ListingCategory;
use App\Models\Location\State;
use App\Models\Location\City;
use App\Models\Location\Neighborhood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CategoryLocationPageController extends Controller
{
    /**
     * Show page by category only
     * URL: /r/{category-slug}
     */
    public function showByCategory($category)
    {
        $misc = new MiscellaneousController();
        $language = $misc->getLanguage();

        // Check if it's a city first (priority to location)
        $cityModel = City::where(function($query) use ($category) {
                $query->where('slug', $category)
                      ->orWhere('name', $category);
            })
            ->first();
        
        if ($cityModel) {
            // Redirect to city page
            return redirect()->route('frontend.category_location_page.city', ['city' => $category]);
        }

        // Find category by slug or name
        $categoryModel = ListingCategory::where(function($query) use ($category) {
                $query->where('slug', $category)
                      ->orWhere('name', $category);
            })
            ->where('status', 1)
            ->firstOrFail();

        // Find or create page
        $page = CategoryLocationPage::where('category_id', $categoryModel->id)
            ->whereNull('state_id')
            ->whereNull('city_id')
            ->whereNull('neighborhood_id')
            ->where('status', 1)
            ->first();

        // If page doesn't exist, create a dynamic one
        if (!$page) {
            $page = (object)[
                'id' => null,
                'category_id' => $categoryModel->id,
                'state_id' => null,
                'city_id' => null,
                'neighborhood_id' => null,
                'title' => $categoryModel->name,
                'meta_title' => $categoryModel->meta_title ?? $categoryModel->name,
                'meta_description' => $categoryModel->meta_description ?? '',
                'meta_keywords' => $categoryModel->meta_keywords ?? '',
                'description' => $categoryModel->description ?? '',
                'category' => $categoryModel,
                'state' => null,
                'city' => null,
                'neighborhood' => null,
            ];
        } else {
            $page->load(['category', 'state', 'city', 'neighborhood']);
        }

        // Build query for listings
        $query = ListingContent::join('listings', 'listings.id', '=', 'listing_contents.listing_id')
            ->join('listing_categories', 'listing_categories.id', '=', 'listing_contents.category_id')
            ->where('listing_contents.language_id', $language->id)
            ->where('listing_contents.category_id', $categoryModel->id);

        $listings = $this->applyListingFilters($query);

        $information = [
            'page' => $page,
            'listings' => $listings,
            'language' => $language,
            'bgImg' => $misc->getBreadcrumb(),
            'pageHeading' => $misc->getPageHeading($language),
        ];

        return view('frontend.category_location_page.show', $information);
    }

    /**
     * Show page by category and city
     * URL: /r/{category-slug}/{city-slug}
     */
    public function showByCategoryCity($category, $city)
    {
        $misc = new MiscellaneousController();
        $language = $misc->getLanguage();

        // Check if first parameter is actually a city (not category)
        $cityCheck = City::where(function($query) use ($category) {
                $query->where('slug', $category)
                      ->orWhere('name', $category);
            })
            ->first();
        
        if ($cityCheck) {
            // First parameter is a city, second might be neighborhood
            $neighborhoodCheck = Neighborhood::where('city_id', $cityCheck->id)
                ->where(function($query) use ($city) {
                    $query->where('slug', $city)
                          ->orWhere('name', $city);
                })
                ->first();
            
            if ($neighborhoodCheck) {
                // It's actually city/neighborhood, redirect
                return redirect()->route('frontend.category_location_page.city_neighborhood', [
                    'city' => $category,
                    'neighborhood' => $city
                ]);
            } else {
                // It's just city, redirect
                return redirect()->route('frontend.category_location_page.city', ['city' => $category]);
            }
        }

        // Find category by slug or name
        $categoryModel = ListingCategory::where(function($query) use ($category) {
                $query->where('slug', $category)
                      ->orWhere('name', $category);
            })
            ->where('status', 1)
            ->firstOrFail();

        // Find city by slug or name
        $cityModel = City::where(function($query) use ($city) {
                $query->where('slug', $city)
                      ->orWhere('name', $city);
            })
            ->firstOrFail();

        // Find or create page
        $page = CategoryLocationPage::where('category_id', $categoryModel->id)
            ->where('city_id', $cityModel->id)
            ->whereNull('neighborhood_id')
            ->where('status', 1)
            ->first();

        // If page doesn't exist, create a dynamic one
        if (!$page) {
            $page = (object)[
                'id' => null,
                'category_id' => $categoryModel->id,
                'state_id' => $cityModel->state_id,
                'city_id' => $cityModel->id,
                'neighborhood_id' => null,
                'title' => $categoryModel->name . ' در ' . $cityModel->name,
                'meta_title' => $categoryModel->name . ' در ' . $cityModel->name,
                'meta_description' => 'بهترین ' . $categoryModel->name . ' در ' . $cityModel->name,
                'meta_keywords' => $categoryModel->name . ', ' . $cityModel->name,
                'description' => '',
                'category' => $categoryModel,
                'state' => $cityModel->state,
                'city' => $cityModel,
                'neighborhood' => null,
            ];
        } else {
            $page->load(['category', 'state', 'city', 'neighborhood']);
        }

        // Build query for listings
        $query = ListingContent::join('listings', 'listings.id', '=', 'listing_contents.listing_id')
            ->join('listing_categories', 'listing_categories.id', '=', 'listing_contents.category_id')
            ->where('listing_contents.language_id', $language->id)
            ->where('listing_contents.category_id', $categoryModel->id)
            ->where('listing_contents.city_id', $cityModel->id);

        $listings = $this->applyListingFilters($query);

        $information = [
            'page' => $page,
            'listings' => $listings,
            'language' => $language,
            'bgImg' => $misc->getBreadcrumb(),
            'pageHeading' => $misc->getPageHeading($language),
        ];

        return view('frontend.category_location_page.show', $information);
    }

    /**
     * Show page by category, city and neighborhood
     * URL: /r/{category-slug}/{city-slug}/{neighborhood-slug}
     */
    public function showByCategoryCityNeighborhood($category, $city, $neighborhood)
    {
        $misc = new MiscellaneousController();
        $language = $misc->getLanguage();

        // Find category by slug or name
        $categoryModel = ListingCategory::where(function($query) use ($category) {
                $query->where('slug', $category)
                      ->orWhere('name', $category);
            })
            ->where('status', 1)
            ->firstOrFail();

        // Find city by slug or name
        $cityModel = City::where(function($query) use ($city) {
                $query->where('slug', $city)
                      ->orWhere('name', $city);
            })
            ->firstOrFail();

        // Find neighborhood by slug or name
        $neighborhoodModel = Neighborhood::where('city_id', $cityModel->id)
            ->where(function($query) use ($neighborhood) {
                // Try both slug and name
                $query->where('name', $neighborhood)
                      ->orWhere('slug', $neighborhood);
            })
            ->firstOrFail();

        // Find or create page
        $page = CategoryLocationPage::where('category_id', $categoryModel->id)
            ->where('city_id', $cityModel->id)
            ->where('neighborhood_id', $neighborhoodModel->id)
            ->where('status', 1)
            ->first();

        // If page doesn't exist, create a dynamic one
        if (!$page) {
            $page = (object)[
                'id' => null,
                'category_id' => $categoryModel->id,
                'state_id' => $cityModel->state_id,
                'city_id' => $cityModel->id,
                'neighborhood_id' => $neighborhoodModel->id,
                'title' => $categoryModel->name . ' در ' . $neighborhoodModel->name . ' ' . $cityModel->name,
                'meta_title' => $categoryModel->name . ' در ' . $neighborhoodModel->name . ' ' . $cityModel->name,
                'meta_description' => 'بهترین ' . $categoryModel->name . ' در ' . $neighborhoodModel->name . ' ' . $cityModel->name,
                'meta_keywords' => $categoryModel->name . ', ' . $neighborhoodModel->name . ', ' . $cityModel->name,
                'description' => '',
                'category' => $categoryModel,
                'state' => $cityModel->state,
                'city' => $cityModel,
                'neighborhood' => $neighborhoodModel,
            ];
        } else {
            $page->load(['category', 'state', 'city', 'neighborhood']);
        }

        // Build query for listings
        $query = ListingContent::join('listings', 'listings.id', '=', 'listing_contents.listing_id')
            ->join('listing_categories', 'listing_categories.id', '=', 'listing_contents.category_id')
            ->where('listing_contents.language_id', $language->id)
            ->where('listing_contents.category_id', $categoryModel->id)
            ->where('listing_contents.city_id', $cityModel->id)
            ->where('listing_contents.neighborhood_id', $neighborhoodModel->id);

        $listings = $this->applyListingFilters($query);

        $information = [
            'page' => $page,
            'listings' => $listings,
            'language' => $language,
            'bgImg' => $misc->getBreadcrumb(),
            'pageHeading' => $misc->getPageHeading($language),
        ];

        return view('frontend.category_location_page.show', $information);
    }

    /**
     * Show page by city only (all categories)
     * URL: /r/{city-slug}
     */
    public function showByCity($city)
    {
        $misc = new MiscellaneousController();
        $language = $misc->getLanguage();

        // Find city by slug or name
        $cityModel = City::where(function($query) use ($city) {
                $query->where('slug', $city)
                      ->orWhere('name', $city);
            })
            ->firstOrFail();

        // Create dynamic page object
        $page = (object)[
            'id' => null,
            'category_id' => null,
            'state_id' => $cityModel->state_id,
            'city_id' => $cityModel->id,
            'neighborhood_id' => null,
            'title' => 'همه کسب‌وکارهای ' . $cityModel->name,
            'meta_title' => 'همه کسب‌وکارهای ' . $cityModel->name,
            'meta_description' => 'لیست کامل همه کسب‌وکارها و خدمات در ' . $cityModel->name,
            'meta_keywords' => $cityModel->name . ', کسب‌وکار, خدمات',
            'description' => '',
            'category' => null,
            'state' => $cityModel->state,
            'city' => $cityModel,
            'neighborhood' => null,
        ];

        // Build query for listings - ALL categories
        $query = ListingContent::join('listings', 'listings.id', '=', 'listing_contents.listing_id')
            ->join('listing_categories', 'listing_categories.id', '=', 'listing_contents.category_id')
            ->where('listing_contents.language_id', $language->id)
            ->where('listing_contents.city_id', $cityModel->id);

        $listings = $this->applyListingFilters($query);

        $information = [
            'page' => $page,
            'listings' => $listings,
            'language' => $language,
            'bgImg' => $misc->getBreadcrumb(),
            'pageHeading' => $misc->getPageHeading($language),
        ];

        return view('frontend.category_location_page.show', $information);
    }

    /**
     * Show page by city and neighborhood (all categories)
     * URL: /r/{city-slug}/{neighborhood-slug}
     */
    public function showByCityNeighborhood($city, $neighborhood)
    {
        $misc = new MiscellaneousController();
        $language = $misc->getLanguage();

        // Find city by slug or name
        $cityModel = City::where(function($query) use ($city) {
                $query->where('slug', $city)
                      ->orWhere('name', $city);
            })
            ->firstOrFail();

        // Find neighborhood by slug or name
        $neighborhoodModel = Neighborhood::where('city_id', $cityModel->id)
            ->where(function($query) use ($neighborhood) {
                $query->where('slug', $neighborhood)
                      ->orWhere('name', $neighborhood);
            })
            ->firstOrFail();

        // Create dynamic page object
        $page = (object)[
            'id' => null,
            'category_id' => null,
            'state_id' => $cityModel->state_id,
            'city_id' => $cityModel->id,
            'neighborhood_id' => $neighborhoodModel->id,
            'title' => 'همه کسب‌وکارهای ' . $neighborhoodModel->name . ' ' . $cityModel->name,
            'meta_title' => 'همه کسب‌وکارهای ' . $neighborhoodModel->name . ' ' . $cityModel->name,
            'meta_description' => 'لیست کامل همه کسب‌وکارها و خدمات در ' . $neighborhoodModel->name . ' ' . $cityModel->name,
            'meta_keywords' => $neighborhoodModel->name . ', ' . $cityModel->name . ', کسب‌وکار, خدمات',
            'description' => '',
            'category' => null,
            'state' => $cityModel->state,
            'city' => $cityModel,
            'neighborhood' => $neighborhoodModel,
        ];

        // Build query for listings - ALL categories
        $query = ListingContent::join('listings', 'listings.id', '=', 'listing_contents.listing_id')
            ->join('listing_categories', 'listing_categories.id', '=', 'listing_contents.category_id')
            ->where('listing_contents.language_id', $language->id)
            ->where('listing_contents.city_id', $cityModel->id)
            ->where('listing_contents.neighborhood_id', $neighborhoodModel->id);

        $listings = $this->applyListingFilters($query);

        $information = [
            'page' => $page,
            'listings' => $listings,
            'language' => $language,
            'bgImg' => $misc->getBreadcrumb(),
            'pageHeading' => $misc->getPageHeading($language),
        ];

        return view('frontend.category_location_page.show', $information);
    }

    /**
     * Apply common filters to listing query
     */
    private function applyListingFilters($query)
    {
        return $query
            ->where('listings.status', 1)
            ->where('listings.visibility', 1)
            ->when('listings.user_id' != "0", function ($query) {
                return $query->leftJoin('memberships', 'listings.user_id', '=', 'memberships.user_id')
                    ->where(function ($query) {
                        $query->where([
                            ['memberships.status', '=', 1],
                            ['memberships.start_date', '<=', now()->format('Y-m-d')],
                            ['memberships.expire_date', '>=', now()->format('Y-m-d')],
                        ])->orWhere('listings.user_id', '=', 0);
                    });
            })
            ->when('listings.user_id' != "0", function ($query) {
                return $query->leftJoin('users', 'listings.user_id', '=', 'users.id')
                    ->where(function ($query) {
                        $query->where([
                            ['users.status', '=', 1],
                        ])->orWhere('listings.user_id', '=', 0);
                    });
            })
            ->select(
                'listings.*',
                'listing_contents.title',
                'listing_contents.slug',
                'listing_contents.category_id',
                'listing_contents.city_id',
                'listing_contents.state_id',
                'listing_contents.neighborhood_id',
                'listing_contents.description',
                'listing_contents.address',
                'listing_categories.name as category_name',
                'listing_categories.icon as icon'
            )
            ->orderBy('listings.id', 'desc')
            ->paginate(12);
    }
}
