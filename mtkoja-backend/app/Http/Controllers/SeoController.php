<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Category;
use App\Models\Province;
use App\Models\City;
use App\Models\Neighborhood;
use App\Models\SeoPage;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SeoController extends Controller
{
    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }
    /**
     * Show businesses by category (all country)
     */
    public function showCategory(string $categorySlug): JsonResponse
    {
        $category = Category::where('slug', $categorySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $businesses = Business::with(['category', 'province', 'city', 'neighborhood', 'user'])
            ->where('category_id', $category->id)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->orderBy('is_featured', 'desc')
            ->orderBy('rating', 'desc')
            ->paginate(20);

        $seoData = $this->generateSeoData([
            'type' => 'category',
            'category' => $category,
            'location' => null,
        ]);

        // Override with custom SEO page if exists
        $seoPage = $this->findSeoPage($category->id, null, null, null);
        if ($seoPage) {
            $seoData = $this->applySeoPageOverrides($seoData, $seoPage);
        }

        return response()->json([
            'businesses' => $businesses,
            'category' => $category,
            'seo' => $seoData,
            'breadcrumbs' => $this->generateBreadcrumbs([
                ['name' => 'خانه', 'url' => '/'],
                ['name' => $category->name, 'url' => "/b/{$category->slug}"],
            ]),
            'json_ld' => $this->generateListingJsonLd($category, null, null, null),
        ]);
    }

    /**
     * Show businesses by category and city
     */
    public function showCategoryByCity(string $categorySlug, string $citySlug): JsonResponse
    {
        $category = Category::where('slug', $categorySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $city = City::with('province')
            ->where('slug', $citySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $businesses = Business::with(['category', 'province', 'city', 'neighborhood', 'user'])
            ->where('category_id', $category->id)
            ->where('city_id', $city->id)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->orderBy('is_featured', 'desc')
            ->orderBy('rating', 'desc')
            ->paginate(20);

        $seoData = $this->generateSeoData([
            'type' => 'category_city',
            'category' => $category,
            'city' => $city,
            'province' => $city->province,
        ]);

        // Override with custom SEO page if exists (city-level)
        $seoPage = $this->findSeoPage($category->id, $city->province->id, $city->id, null);
        if ($seoPage) {
            $seoData = $this->applySeoPageOverrides($seoData, $seoPage);
        }

        return response()->json([
            'businesses' => $businesses,
            'category' => $category,
            'city' => $city,
            'province' => $city->province,
            'seo' => $seoData,
            'breadcrumbs' => $this->generateBreadcrumbs([
                ['name' => 'خانه', 'url' => '/'],
                ['name' => $category->name, 'url' => "/b/{$category->slug}"],
                ['name' => $city->name, 'url' => "/b/{$category->slug}/{$city->slug}"],
            ]),
            'json_ld' => $this->generateListingJsonLd($category, $city->province, $city, null),
        ]);
    }

    /**
     * Show businesses by category, city and neighborhood
     */
    public function showCategoryByNeighborhood(string $categorySlug, string $citySlug, string $neighborhoodSlug): JsonResponse
    {
        $category = Category::where('slug', $categorySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $city = City::with('province')
            ->where('slug', $citySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $neighborhood = Neighborhood::where('city_id', $city->id)
            ->where('slug', $neighborhoodSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $businesses = Business::with(['category', 'province', 'city', 'neighborhood', 'user'])
            ->where('category_id', $category->id)
            ->where('city_id', $city->id)
            ->where('neighborhood_id', $neighborhood->id)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->orderBy('is_featured', 'desc')
            ->orderBy('rating', 'desc')
            ->paginate(20);

        $seoData = $this->generateSeoData([
            'type' => 'category_city_neighborhood',
            'category' => $category,
            'city' => $city,
            'province' => $city->province,
            'neighborhood' => $neighborhood,
        ]);

        // Override with custom SEO page if exists (neighborhood-level)
        $seoPage = $this->findSeoPage($category->id, $city->province->id, $city->id, $neighborhood->id);
        if ($seoPage) {
            $seoData = $this->applySeoPageOverrides($seoData, $seoPage);
        }

        return response()->json([
            'businesses' => $businesses,
            'category' => $category,
            'city' => $city,
            'province' => $city->province,
            'neighborhood' => $neighborhood,
            'seo' => $seoData,
            'breadcrumbs' => $this->generateBreadcrumbs([
                ['name' => 'خانه', 'url' => '/'],
                ['name' => $category->name, 'url' => "/b/{$category->slug}"],
                ['name' => $city->name, 'url' => "/b/{$category->slug}/{$city->slug}"],
                ['name' => $neighborhood->name, 'url' => "/b/{$category->slug}/{$city->slug}/{$neighborhood->slug}"],
            ]),
            'json_ld' => $this->generateListingJsonLd($category, $city->province, $city, $neighborhood),
        ]);
    }

    /**
     * Show business profile
     */
    public function showBusiness(string $businessSlug): JsonResponse
    {
        $business = Business::with(['category', 'province', 'city', 'neighborhood', 'user', 'reviews', 'features'])
            ->where('slug', $businessSlug)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->firstOrFail();

        // Increment view count
        $business->increment('view_count');

        $seoData = $this->generateBusinessSeoData($business);

        return response()->json([
            'business' => $business,
            'seo' => $seoData,
            'breadcrumbs' => $this->generateBusinessBreadcrumbs($business),
            'json_ld' => $this->generateJsonLd($business),
        ]);
    }

    /**
     * Generate SEO data for business listings
     */
    private function generateSeoData(array $data): array
    {
        $type = $data['type'];
        $category = $data['category'];
        $city = $data['city'] ?? null;
        $neighborhood = $data['neighborhood'] ?? null;

        $title = $this->seoService->generateCategoryMetaTitle($category, $city, $neighborhood);
        $description = $this->seoService->generateCategoryMetaDescription($category, $city, $neighborhood);
        $keywords = $this->seoService->generateCategoryKeywords($category, $city, $neighborhood);

        $seoData = [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'canonical' => request()->url(),
            'h1' => $title,
            'content' => null,
            'custom_text' => null,
        ];

        // اضافه کردن Open Graph Tags
        $seoData = array_merge($seoData, $this->seoService->generateOpenGraphTags([
            'title' => $title,
            'description' => $description,
            'canonical' => request()->url(),
            'og_type' => 'website',
            'og_image' => null
        ]));

        // اضافه کردن Twitter Card Tags
        $seoData = array_merge($seoData, $this->seoService->generateTwitterCardTags([
            'title' => $title,
            'description' => $description,
            'og_image' => null
        ]));

        return $seoData;
    }

    /**
     * Generate SEO data for business profile
     */
    private function generateBusinessSeoData(Business $business): array
    {
        $title = $this->seoService->generateBusinessMetaTitle($business);
        $description = $this->seoService->generateBusinessMetaDescription($business);
        $keywords = $this->seoService->generateBusinessKeywords($business);

        $seoData = [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'canonical' => request()->url(),
            'h1' => $business->name,
            'content' => $business->description,
            'custom_text' => null,
        ];

        // اضافه کردن Open Graph Tags
        $seoData = array_merge($seoData, $this->seoService->generateOpenGraphTags([
            'title' => $title,
            'description' => $description,
            'canonical' => request()->url(),
            'og_type' => 'business.business',
            'og_image' => $business->primaryImage?->url ?? url('images/default-business.jpg')
        ]));

        // اضافه کردن Twitter Card Tags
        $seoData = array_merge($seoData, $this->seoService->generateTwitterCardTags([
            'title' => $title,
            'description' => $description,
            'og_image' => $business->primaryImage?->url ?? url('images/default-business.jpg')
        ]));

        return $seoData;
    }

    /**
     * Generate breadcrumbs
     */
    private function generateBreadcrumbs(array $items): array
    {
        return $items;
    }

    /**
     * Generate business breadcrumbs
     */
    private function generateBusinessBreadcrumbs(Business $business): array
    {
        $breadcrumbs = [
            ['name' => 'خانه', 'url' => '/'],
            ['name' => $business->category->name, 'url' => "/b/{$business->category->slug}"],
            ['name' => $business->city->name, 'url' => "/b/{$business->category->slug}/{$business->city->slug}"],
            ['name' => $business->name, 'url' => "/business/{$business->slug}"],
        ];

        // اضافه کردن Schema Markup برای breadcrumbs
        return [
            'items' => $breadcrumbs,
            'schema' => $this->seoService->generateBreadcrumbSchema($breadcrumbs)
        ];
    }

    /**
     * Generate JSON-LD structured data
     */
    private function generateJsonLd(Business $business): array
    {
        return $this->seoService->generateCompleteStructuredData($business);
    }

    /**
     * Format opening hours for JSON-LD
     */
    private function formatOpeningHours(array $workingHours): array
    {
        $formatted = [];
        $days = [
            'monday' => 'Mo',
            'tuesday' => 'Tu',
            'wednesday' => 'We',
            'thursday' => 'Th',
            'friday' => 'Fr',
            'saturday' => 'Sa',
            'sunday' => 'Su',
        ];

        foreach ($workingHours as $day => $hours) {
            if ($hours && $hours !== 'تعطیل') {
                $formatted[] = "{$days[$day]} {$hours}";
            }
        }

        return $formatted;
    }

    /**
     * Find a custom SEO page for the given combination
     */
    private function findSeoPage(int $categoryId, ?int $provinceId, ?int $cityId, ?int $neighborhoodId): ?SeoPage
    {
        // Priority: neighborhood > city > province > category-only
        if ($neighborhoodId) {
            $page = SeoPage::where('category_id', $categoryId)
                ->where('city_id', $cityId)
                ->where('neighborhood_id', $neighborhoodId)
                ->first();
            if ($page) return $page;
        }

        if ($cityId) {
            $page = SeoPage::where('category_id', $categoryId)
                ->where('city_id', $cityId)
                ->whereNull('neighborhood_id')
                ->first();
            if ($page) return $page;
        }

        if ($provinceId) {
            $page = SeoPage::where('category_id', $categoryId)
                ->where('province_id', $provinceId)
                ->whereNull('city_id')
                ->whereNull('neighborhood_id')
                ->first();
            if ($page) return $page;
        }

        // Category-only fallback
        return SeoPage::where('category_id', $categoryId)
            ->whereNull('province_id')
            ->whereNull('city_id')
            ->whereNull('neighborhood_id')
            ->first();
    }

    /**
     * Apply overrides from SeoPage model into seo data array
     */
    private function applySeoPageOverrides(array $seoData, SeoPage $page): array
    {
        $seoData['title'] = $page->title ?: $seoData['title'];
        $seoData['description'] = $page->meta_description ?: $seoData['description'];
        $seoData['og_title'] = $seoData['title'];
        $seoData['og_description'] = $seoData['description'];
        if (!empty($page->og_image)) {
            $seoData['og_image'] = $page->og_image;
        }
        $seoData['h1'] = $page->h1;
        $seoData['content'] = $page->content;
        $seoData['custom_text'] = $page->custom_text;
        if (!empty($page->keywords)) {
            $seoData['keywords'] = $page->keywords;
        }
        return $seoData;
    }

    /**
     * Generate JSON-LD for listings pages
     */
    private function generateListingJsonLd(Category $category, ?Province $province, ?City $city, ?Neighborhood $neighborhood): array
    {
        $nameParts = [$category->name];
        if ($neighborhood) { $nameParts[] = $neighborhood->name; }
        if ($city) { $nameParts[] = $city->name; }
        if ($province && !$city) { $nameParts[] = $province->name; }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => implode(' - ', $nameParts),
            'description' => $this->generateSeoData([
                'type' => $neighborhood ? 'category_city_neighborhood' : ($city ? 'category_city' : 'category'),
                'category' => $category,
                'city' => $city,
                'province' => $province,
                'neighborhood' => $neighborhood,
            ])['description'],
            'url' => request()->url(),
        ];
    }
}