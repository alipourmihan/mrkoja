<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Category;
use App\Models\Province;
use App\Models\City;
use App\Models\Neighborhood;
use App\Models\SeoPage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class SeoManagerController extends Controller
{
    /**
     * تولید Sitemap XML
     */
    public function generateSitemap(): JsonResponse
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // صفحه اصلی
        $sitemap .= $this->generateSitemapUrl('/', '1.0', 'daily');

        // صفحات دسته‌بندی
        $categories = Category::where('is_active', true)->get();
        foreach ($categories as $category) {
            $sitemap .= $this->generateSitemapUrl("/b/{$category->slug}", '0.8', 'weekly');
        }

        // صفحات دسته‌بندی + شهر
        $cities = City::where('is_active', true)->with('province')->get();
        foreach ($categories as $category) {
            foreach ($cities as $city) {
                $sitemap .= $this->generateSitemapUrl("/b/{$category->slug}/{$city->slug}", '0.7', 'weekly');
            }
        }

        // صفحات کسب‌وکار
        $businesses = Business::where('status', 'approved')
            ->where('is_active', true)
            ->get();
        foreach ($businesses as $business) {
            $sitemap .= $this->generateSitemapUrl("/business/{$business->slug}", '0.6', 'monthly');
        }

        $sitemap .= '</urlset>';

        // ذخیره فایل
        Storage::disk('public')->put('sitemap.xml', $sitemap);

        return response()->json([
            'message' => 'Sitemap با موفقیت تولید شد',
            'url' => url('storage/sitemap.xml')
        ]);
    }

    /**
     * تولید robots.txt
     */
    public function generateRobotsTxt(): JsonResponse
    {
        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n";
        $robots .= "Disallow: /admin/\n";
        $robots .= "Disallow: /api/\n";
        $robots .= "Disallow: /storage/private/\n";
        $robots .= "\n";
        $robots .= "Sitemap: " . url('storage/sitemap.xml') . "\n";

        Storage::disk('public')->put('robots.txt', $robots);

        return response()->json([
            'message' => 'robots.txt با موفقیت تولید شد',
            'url' => url('storage/robots.txt')
        ]);
    }

    /**
     * تولید Schema Markup برای صفحه اصلی
     */
    public function generateHomepageSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'متکوجا - راهنمای کسب‌وکارهای محلی',
            'description' => 'پلتفرم جامع برای یافتن بهترین کسب‌وکارهای محلی در سراسر ایران',
            'url' => url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/search?q={search_term_string}'),
                'query-input' => 'required name=search_term_string'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'متکوجا',
                'url' => url('/')
            ]
        ];
    }

    /**
     * تولید Schema Markup برای صفحات دسته‌بندی
     */
    public function generateCategorySchema(Category $category, ?City $city = null, ?Neighborhood $neighborhood = null): array
    {
        $name = $category->name;
        if ($neighborhood) {
            $name .= " در {$neighborhood->name}، {$city->name}";
        } elseif ($city) {
            $name .= " در {$city->name}";
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $name,
            'description' => "لیست {$category->name}" . ($city ? " در {$city->name}" : ''),
            'url' => request()->url(),
            'numberOfItems' => $this->getBusinessCount($category, $city, $neighborhood),
            'itemListElement' => $this->getBusinessListItems($category, $city, $neighborhood)
        ];
    }

    /**
     * تولید Schema Markup برای کسب‌وکار
     */
    public function generateBusinessSchema(Business $business): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $business->name,
            'description' => $business->description,
            'url' => request()->url(),
            'telephone' => $business->phone,
            'email' => $business->email,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $business->address,
                'addressLocality' => $business->city->name,
                'addressRegion' => $business->province->name,
                'postalCode' => $business->postal_code ?? '',
                'addressCountry' => 'IR'
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => $business->latitude,
                'longitude' => $business->longitude
            ]
        ];

        // اضافه کردن امتیاز و نظرات
        if ($business->rating > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $business->rating,
                'reviewCount' => $business->reviews_count
            ];
        }

        // اضافه کردن ساعت کاری
        if ($business->working_hours) {
            $schema['openingHours'] = $this->formatWorkingHours($business->working_hours);
        }

        // اضافه کردن تصاویر
        if ($business->images()->count() > 0) {
            $schema['image'] = $business->images->map(function ($image) {
                return url('storage/' . $image->path);
            })->toArray();
        }

        return $schema;
    }

    /**
     * اعتبارسنجی SEO
     */
    public function validateSeo(Request $request): JsonResponse
    {
        $url = $request->input('url');
        $type = $request->input('type', 'business'); // business, category, homepage

        $validation = [
            'url' => $url,
            'type' => $type,
            'issues' => [],
            'score' => 100
        ];

        // بررسی طول عنوان
        if (strlen($request->input('title', '')) > 60) {
            $validation['issues'][] = 'عنوان بیش از 60 کاراکتر است';
            $validation['score'] -= 10;
        }

        // بررسی طول توضیحات
        $description = $request->input('description', '');
        if (strlen($description) < 120 || strlen($description) > 160) {
            $validation['issues'][] = 'توضیحات باید بین 120 تا 160 کاراکتر باشد';
            $validation['score'] -= 15;
        }

        // بررسی وجود H1
        if (empty($request->input('h1'))) {
            $validation['issues'][] = 'عنوان H1 وجود ندارد';
            $validation['score'] -= 20;
        }

        // بررسی کلمات کلیدی
        if (empty($request->input('keywords'))) {
            $validation['issues'][] = 'کلمات کلیدی تعریف نشده‌اند';
            $validation['score'] -= 10;
        }

        return response()->json($validation);
    }

    /**
     * آمار SEO
     */
    public function getSeoStats(): JsonResponse
    {
        $stats = Cache::remember('seo_stats', 3600, function () {
            return [
                'total_businesses' => Business::where('status', 'approved')->count(),
                'businesses_with_seo' => Business::where('status', 'approved')
                    ->whereNotNull('meta_title')
                    ->whereNotNull('meta_description')
                    ->count(),
                'total_categories' => Category::where('is_active', true)->count(),
                'categories_with_seo' => Category::where('is_active', true)
                    ->whereNotNull('meta_title')
                    ->whereNotNull('meta_description')
                    ->count(),
                'seo_pages_count' => SeoPage::count(),
                'last_sitemap_generation' => Storage::disk('public')->exists('sitemap.xml') 
                    ? Storage::disk('public')->lastModified('sitemap.xml') 
                    : null
            ];
        });

        return response()->json($stats);
    }

    /**
     * تولید URL برای Sitemap
     */
    private function generateSitemapUrl(string $url, string $priority, string $changefreq): string
    {
        $fullUrl = url($url);
        $lastmod = date('Y-m-d');
        
        return "  <url>\n" .
               "    <loc>{$fullUrl}</loc>\n" .
               "    <lastmod>{$lastmod}</lastmod>\n" .
               "    <changefreq>{$changefreq}</changefreq>\n" .
               "    <priority>{$priority}</priority>\n" .
               "  </url>\n";
    }

    /**
     * تعداد کسب‌وکارها
     */
    private function getBusinessCount(Category $category, ?City $city = null, ?Neighborhood $neighborhood = null): int
    {
        $query = Business::where('category_id', $category->id)
            ->where('status', 'approved')
            ->where('is_active', true);

        if ($city) {
            $query->where('city_id', $city->id);
        }

        if ($neighborhood) {
            $query->where('neighborhood_id', $neighborhood->id);
        }

        return $query->count();
    }

    /**
     * آیتم‌های لیست کسب‌وکارها
     */
    private function getBusinessListItems(Category $category, ?City $city = null, ?Neighborhood $neighborhood = null): array
    {
        $query = Business::where('category_id', $category->id)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->limit(10);

        if ($city) {
            $query->where('city_id', $city->id);
        }

        if ($neighborhood) {
            $query->where('neighborhood_id', $neighborhood->id);
        }

        $businesses = $query->get();

        return $businesses->map(function ($business, $index) {
            return [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@type' => 'LocalBusiness',
                    'name' => $business->name,
                    'url' => url("/business/{$business->slug}")
                ]
            ];
        })->toArray();
    }

    /**
     * فرمت ساعت کاری
     */
    private function formatWorkingHours($workingHours): array
    {
        if (is_string($workingHours)) {
            $workingHours = json_decode($workingHours, true);
        }

        $formatted = [];
        $days = [
            'monday' => 'Mo',
            'tuesday' => 'Tu',
            'wednesday' => 'We',
            'thursday' => 'Th',
            'friday' => 'Fr',
            'saturday' => 'Sa',
            'sunday' => 'Su'
        ];

        foreach ($workingHours as $day => $hours) {
            if ($hours && $hours !== 'تعطیل') {
                $formatted[] = "{$days[$day]} {$hours}";
            }
        }

        return $formatted;
    }
}

