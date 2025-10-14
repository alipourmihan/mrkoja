<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Category;
use App\Models\SeoPage;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class SeoAdminController extends Controller
{
    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    /**
     * داشبورد SEO
     */
    public function dashboard(): JsonResponse
    {
        $stats = Cache::remember('seo_dashboard_stats', 3600, function () {
            $totalBusinesses = Business::count();
            $businessesWithSeo = Business::whereNotNull('meta_title')
                ->whereNotNull('meta_description')
                ->count();
            
            $totalCategories = Category::count();
            $categoriesWithSeo = Category::whereNotNull('meta_title')
                ->whereNotNull('meta_description')
                ->count();

            $seoPagesCount = SeoPage::count();
            $indexedBusinesses = Business::where('is_indexed', true)->count();
            $indexedCategories = Category::where('is_indexed', true)->count();

            // آمار Meta Tags
            $metaTitleStats = [
                'businesses' => Business::whereNotNull('meta_title')->count(),
                'categories' => Category::whereNotNull('meta_title')->count(),
            ];

            $metaDescriptionStats = [
                'businesses' => Business::whereNotNull('meta_description')->count(),
                'categories' => Category::whereNotNull('meta_description')->count(),
            ];

            $metaKeywordsStats = [
                'businesses' => Business::whereNotNull('meta_keywords')->count(),
                'categories' => Category::whereNotNull('meta_keywords')->count(),
            ];

            // آمار Open Graph
            $ogStats = [
                'businesses' => Business::whereNotNull('og_title')->count(),
                'categories' => Category::whereNotNull('og_title')->count(),
            ];

            // آمار Twitter Card
            $twitterStats = [
                'businesses' => Business::whereNotNull('twitter_title')->count(),
                'categories' => Category::whereNotNull('twitter_title')->count(),
            ];

            // آمار Schema Markup
            $schemaStats = [
                'businesses_with_schema' => Business::whereNotNull('meta_title')
                    ->whereNotNull('meta_description')
                    ->whereNotNull('address')
                    ->whereNotNull('phone')
                    ->count(),
                'categories_with_schema' => Category::whereNotNull('meta_title')
                    ->whereNotNull('meta_description')
                    ->count(),
            ];

            // وضعیت Sitemap و robots.txt
            $sitemapExists = Storage::disk('public')->exists('sitemap.xml');
            $robotsExists = Storage::disk('public')->exists('robots.txt');

            return [
                'overview' => [
                    'total_businesses' => $totalBusinesses,
                    'businesses_with_seo' => $businessesWithSeo,
                    'businesses_seo_percentage' => $totalBusinesses > 0 ? round(($businessesWithSeo / $totalBusinesses) * 100, 2) : 0,
                    'total_categories' => $totalCategories,
                    'categories_with_seo' => $categoriesWithSeo,
                    'categories_seo_percentage' => $totalCategories > 0 ? round(($categoriesWithSeo / $totalCategories) * 100, 2) : 0,
                    'seo_pages_count' => $seoPagesCount,
                    'indexed_businesses' => $indexedBusinesses,
                    'indexed_categories' => $indexedCategories,
                ],
                'meta_tags' => [
                    'title' => $metaTitleStats,
                    'description' => $metaDescriptionStats,
                    'keywords' => $metaKeywordsStats,
                ],
                'social_media' => [
                    'open_graph' => $ogStats,
                    'twitter_card' => $twitterStats,
                ],
                'schema_markup' => $schemaStats,
                'technical' => [
                    'sitemap_exists' => $sitemapExists,
                    'robots_exists' => $robotsExists,
                    'last_sitemap_update' => $sitemapExists ? Storage::disk('public')->lastModified('sitemap.xml') : null,
                    'last_robots_update' => $robotsExists ? Storage::disk('public')->lastModified('robots.txt') : null,
                ],
                'charts' => [
                    'seo_completion' => [
                        'businesses' => [
                            'complete' => $businessesWithSeo,
                            'incomplete' => $totalBusinesses - $businessesWithSeo,
                        ],
                        'categories' => [
                            'complete' => $categoriesWithSeo,
                            'incomplete' => $totalCategories - $categoriesWithSeo,
                        ],
                    ],
                    'meta_tags_distribution' => [
                        'title' => $metaTitleStats,
                        'description' => $metaDescriptionStats,
                        'keywords' => $metaKeywordsStats,
                    ],
                ],
            ];
        });

        return response()->json($stats);
    }

    /**
     * لیست تمام صفحات SEO
     */
    public function getAllSeoPages(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);
        $search = $request->input('search');
        $type = $request->input('type'); // 'business', 'category', 'seo_page'
        $status = $request->input('status'); // 'complete', 'incomplete', 'missing'
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $query = collect();

        // کسب‌وکارها
        if (!$type || $type === 'business') {
            $businesses = Business::with(['category', 'city', 'province'])
                ->when($search, function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->when($status === 'complete', function ($q) {
                    $q->whereNotNull('meta_title')
                      ->whereNotNull('meta_description')
                      ->whereNotNull('meta_keywords');
                })
                ->when($status === 'incomplete', function ($q) {
                    $q->where(function ($subQ) {
                        $subQ->whereNull('meta_title')
                             ->orWhereNull('meta_description')
                             ->orWhereNull('meta_keywords');
                    });
                })
                ->when($status === 'missing', function ($q) {
                    $q->whereNull('meta_title')
                      ->whereNull('meta_description')
                      ->whereNull('meta_keywords');
                })
                ->get()
                ->map(function ($business) {
                    return [
                        'id' => $business->id,
                        'type' => 'business',
                        'name' => $business->name,
                        'url' => "/business/{$business->slug}",
                        'category' => $business->category->name ?? null,
                        'location' => $business->city->name ?? null,
                        'meta_title' => $business->meta_title,
                        'meta_description' => $business->meta_description,
                        'meta_keywords' => $business->meta_keywords,
                        'seo_status' => $this->calculateSeoStatus($business),
                        'last_updated' => $business->updated_at,
                        'is_indexed' => $business->is_indexed,
                        'is_followed' => $business->is_followed,
                    ];
                });

            $query = $query->merge($businesses);
        }

        // دسته‌بندی‌ها
        if (!$type || $type === 'category') {
            $categories = Category::when($search, function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->when($status === 'complete', function ($q) {
                    $q->whereNotNull('meta_title')
                      ->whereNotNull('meta_description')
                      ->whereNotNull('meta_keywords');
                })
                ->when($status === 'incomplete', function ($q) {
                    $q->where(function ($subQ) {
                        $subQ->whereNull('meta_title')
                             ->orWhereNull('meta_description')
                             ->orWhereNull('meta_keywords');
                    });
                })
                ->when($status === 'missing', function ($q) {
                    $q->whereNull('meta_title')
                      ->whereNull('meta_description')
                      ->whereNull('meta_keywords');
                })
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'type' => 'category',
                        'name' => $category->name,
                        'url' => "/b/{$category->slug}",
                        'category' => null,
                        'location' => null,
                        'meta_title' => $category->meta_title,
                        'meta_description' => $category->meta_description,
                        'meta_keywords' => $category->meta_keywords,
                        'seo_status' => $this->calculateCategorySeoStatus($category),
                        'last_updated' => $category->updated_at,
                        'is_indexed' => $category->is_indexed ?? true,
                        'is_followed' => $category->is_followed ?? true,
                    ];
                });

            $query = $query->merge($categories);
        }

        // صفحات SEO سفارشی
        if (!$type || $type === 'seo_page') {
            $seoPages = SeoPage::with(['category', 'city', 'neighborhood'])
                ->when($search, function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                })
                ->get()
                ->map(function ($seoPage) {
                    return [
                        'id' => $seoPage->id,
                        'type' => 'seo_page',
                        'name' => $seoPage->title,
                        'url' => $this->generateSeoPageUrl($seoPage),
                        'category' => $seoPage->category->name ?? null,
                        'location' => $this->generateLocationString($seoPage),
                        'meta_title' => $seoPage->title,
                        'meta_description' => $seoPage->meta_description,
                        'meta_keywords' => $seoPage->keywords,
                        'seo_status' => $this->calculateSeoPageStatus($seoPage),
                        'last_updated' => $seoPage->updated_at,
                        'is_indexed' => $seoPage->is_indexed,
                        'is_followed' => $seoPage->is_followed,
                    ];
                });

            $query = $query->merge($seoPages);
        }

        // مرتب‌سازی
        $query = $query->sortBy($sortBy, SORT_REGULAR, $sortOrder === 'desc');

        // صفحه‌بندی
        $total = $query->count();
        $items = $query->forPage($request->input('page', 1), $perPage)->values();

        return response()->json([
            'data' => $items,
            'pagination' => [
                'current_page' => $request->input('page', 1),
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
            ],
            'filters' => [
                'search' => $search,
                'type' => $type,
                'status' => $status,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ]
        ]);
    }

    /**
     * لیست کسب‌وکارهای بدون SEO
     */
    public function businessesWithoutSeo(): JsonResponse
    {
        $businesses = Business::where(function ($query) {
            $query->whereNull('meta_title')
                  ->orWhereNull('meta_description')
                  ->orWhereNull('meta_keywords');
        })
        ->with(['category', 'city', 'province'])
        ->paginate(20);

        return response()->json($businesses);
    }

    /**
     * لیست دسته‌بندی‌های بدون SEO
     */
    public function categoriesWithoutSeo(): JsonResponse
    {
        $categories = Category::where(function ($query) {
            $query->whereNull('meta_title')
                  ->orWhereNull('meta_description')
                  ->orWhereNull('meta_keywords');
        })
        ->paginate(20);

        return response()->json($categories);
    }

    /**
     * تولید خودکار SEO برای کسب‌وکار
     */
    public function generateBusinessSeo(Business $business): JsonResponse
    {
        try {
            $business->update([
                'meta_title' => $this->seoService->generateBusinessMetaTitle($business),
                'meta_description' => $this->seoService->generateBusinessMetaDescription($business),
                'meta_keywords' => $this->seoService->generateBusinessKeywords($business),
                'og_title' => $this->seoService->generateBusinessMetaTitle($business),
                'og_description' => $this->seoService->generateBusinessMetaDescription($business),
                'twitter_title' => $this->seoService->generateBusinessMetaTitle($business),
                'twitter_description' => $this->seoService->generateBusinessMetaDescription($business),
            ]);

            return response()->json([
                'message' => 'SEO برای کسب‌وکار با موفقیت تولید شد',
                'business' => $business->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در تولید SEO برای کسب‌وکار',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تولید خودکار SEO برای دسته‌بندی
     */
    public function generateCategorySeo(Category $category): JsonResponse
    {
        try {
            $category->update([
                'meta_title' => $this->seoService->generateCategoryMetaTitle($category),
                'meta_description' => $this->seoService->generateCategoryMetaDescription($category),
                'meta_keywords' => $this->seoService->generateCategoryKeywords($category),
                'og_title' => $this->seoService->generateCategoryMetaTitle($category),
                'og_description' => $this->seoService->generateCategoryMetaDescription($category),
                'twitter_title' => $this->seoService->generateCategoryMetaTitle($category),
                'twitter_description' => $this->seoService->generateCategoryMetaDescription($category),
                'h1' => $category->name,
            ]);

            return response()->json([
                'message' => 'SEO برای دسته‌بندی با موفقیت تولید شد',
                'category' => $category->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در تولید SEO برای دسته‌بندی',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تولید انبوه SEO
     */
    public function generateBulkSeo(Request $request): JsonResponse
    {
        try {
            $type = $request->input('type'); // 'businesses' or 'categories'
            $ids = $request->input('ids', []);

            $updated = 0;

            if ($type === 'businesses') {
                $businesses = Business::whereIn('id', $ids)->get();
                foreach ($businesses as $business) {
                    $business->update([
                        'meta_title' => $this->seoService->generateBusinessMetaTitle($business),
                        'meta_description' => $this->seoService->generateBusinessMetaDescription($business),
                        'meta_keywords' => $this->seoService->generateBusinessKeywords($business),
                    ]);
                    $updated++;
                }
            } elseif ($type === 'categories') {
                $categories = Category::whereIn('id', $ids)->get();
                foreach ($categories as $category) {
                    $category->update([
                        'meta_title' => $this->seoService->generateCategoryMetaTitle($category),
                        'meta_description' => $this->seoService->generateCategoryMetaDescription($category),
                        'meta_keywords' => $this->seoService->generateCategoryKeywords($category),
                    ]);
                    $updated++;
                }
            }

            return response()->json([
                'message' => "SEO برای {$updated} مورد با موفقیت تولید شد",
                'updated_count' => $updated
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در تولید انبوه SEO',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * اعتبارسنجی SEO
     */
    public function validateSeo(Request $request): JsonResponse
    {
        try {
            $type = $request->input('type');
            $id = $request->input('id');

            $validation = [
                'type' => $type,
                'id' => $id,
                'issues' => [],
                'score' => 100,
                'recommendations' => []
            ];

            if ($type === 'business') {
                $business = Business::findOrFail($id);
                $this->validateBusinessSeo($business, $validation);
            } elseif ($type === 'category') {
                $category = Category::findOrFail($id);
                $this->validateCategorySeo($category, $validation);
            }

            return response()->json([
                'message' => 'اعتبارسنجی SEO انجام شد',
                'validation' => $validation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در اعتبارسنجی SEO',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * اعتبارسنجی SEO کسب‌وکار
     */
    private function validateBusinessSeo(Business $business, array &$validation): void
    {
        // بررسی Meta Title
        if (empty($business->meta_title)) {
            $validation['issues'][] = 'Meta Title خالی است';
            $validation['score'] -= 20;
        } else {
            $titleIssues = $this->seoService->validateMetaTitle($business->meta_title);
            $validation['issues'] = array_merge($validation['issues'], $titleIssues);
            $validation['score'] -= count($titleIssues) * 5;
        }

        // بررسی Meta Description
        if (empty($business->meta_description)) {
            $validation['issues'][] = 'Meta Description خالی است';
            $validation['score'] -= 20;
        } else {
            $descIssues = $this->seoService->validateMetaDescription($business->meta_description);
            $validation['issues'] = array_merge($validation['issues'], $descIssues);
            $validation['score'] -= count($descIssues) * 5;
        }

        // بررسی کلمات کلیدی
        if (empty($business->meta_keywords)) {
            $validation['issues'][] = 'کلمات کلیدی تعریف نشده‌اند';
            $validation['score'] -= 10;
        }

        // بررسی آدرس
        if (empty($business->address)) {
            $validation['issues'][] = 'آدرس کسب‌وکار خالی است';
            $validation['score'] -= 10;
        }

        // بررسی شماره تماس
        if (empty($business->phone)) {
            $validation['issues'][] = 'شماره تماس خالی است';
            $validation['score'] -= 10;
        }

        // بررسی تصاویر
        if ($business->images()->count() === 0) {
            $validation['issues'][] = 'هیچ تصویری برای کسب‌وکار آپلود نشده است';
            $validation['score'] -= 15;
        }

        // توصیه‌ها
        if ($business->rating === 0) {
            $validation['recommendations'][] = 'درخواست نظرات از مشتریان برای بهبود امتیاز';
        }

        if (empty($business->working_hours)) {
            $validation['recommendations'][] = 'تعریف ساعت کاری کسب‌وکار';
        }

        if (empty($business->social_media)) {
            $validation['recommendations'][] = 'اضافه کردن لینک‌های شبکه‌های اجتماعی';
        }
    }

    /**
     * اعتبارسنجی SEO دسته‌بندی
     */
    private function validateCategorySeo(Category $category, array &$validation): void
    {
        // بررسی Meta Title
        if (empty($category->meta_title)) {
            $validation['issues'][] = 'Meta Title خالی است';
            $validation['score'] -= 20;
        } else {
            $titleIssues = $this->seoService->validateMetaTitle($category->meta_title);
            $validation['issues'] = array_merge($validation['issues'], $titleIssues);
            $validation['score'] -= count($titleIssues) * 5;
        }

        // بررسی Meta Description
        if (empty($category->meta_description)) {
            $validation['issues'][] = 'Meta Description خالی است';
            $validation['score'] -= 20;
        } else {
            $descIssues = $this->seoService->validateMetaDescription($category->meta_description);
            $validation['issues'] = array_merge($validation['issues'], $descIssues);
            $validation['score'] -= count($descIssues) * 5;
        }

        // بررسی کلمات کلیدی
        if (empty($category->meta_keywords)) {
            $validation['issues'][] = 'کلمات کلیدی تعریف نشده‌اند';
            $validation['score'] -= 10;
        }

        // بررسی توضیحات
        if (empty($category->description)) {
            $validation['issues'][] = 'توضیحات دسته‌بندی خالی است';
            $validation['score'] -= 15;
        }

        // بررسی کسب‌وکارهای فعال
        $businessCount = $category->businesses()->where('status', 'approved')->count();
        if ($businessCount === 0) {
            $validation['issues'][] = 'هیچ کسب‌وکار فعالی در این دسته‌بندی وجود ندارد';
            $validation['score'] -= 20;
        }

        // توصیه‌ها
        if ($businessCount < 5) {
            $validation['recommendations'][] = 'افزایش تعداد کسب‌وکارهای فعال در این دسته‌بندی';
        }

        if (empty($category->h1)) {
            $validation['recommendations'][] = 'تعریف عنوان H1 برای دسته‌بندی';
        }
    }

    /**
     * تولید Sitemap
     */
    public function generateSitemap(): JsonResponse
    {
        try {
            $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            // صفحه اصلی
            $sitemap .= $this->generateSitemapUrl('/', '1.0', 'daily');

            // صفحات دسته‌بندی
            $categories = Category::where('is_active', true)->get();
            foreach ($categories as $category) {
                $sitemap .= $this->generateSitemapUrl("/b/{$category->slug}", '0.8', 'weekly');
            }

            // صفحات کسب‌وکار
            $businesses = Business::where('status', 'approved')
                ->where('is_active', true)
                ->where('is_indexed', true)
                ->get();
            foreach ($businesses as $business) {
                $sitemap .= $this->generateSitemapUrl("/business/{$business->slug}", '0.6', 'monthly');
            }

            $sitemap .= '</urlset>';

            Storage::disk('public')->put('sitemap.xml', $sitemap);

            return response()->json([
                'message' => 'Sitemap با موفقیت تولید شد',
                'url' => url('storage/sitemap.xml')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در تولید Sitemap',
                'error' => $e->getMessage()
            ], 500);
        }
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
     * ویرایش SEO صفحه
     */
    public function updateSeoPage(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:business,category,seo_page',
            'id' => 'required|integer',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:500',
            'og_title' => 'nullable|string|max:60',
            'og_description' => 'nullable|string|max:160',
            'twitter_title' => 'nullable|string|max:60',
            'twitter_description' => 'nullable|string|max:160',
            'canonical_url' => 'nullable|url',
            'h1' => 'nullable|string|max:255',
            'h2' => 'nullable|string|max:255',
            'h3' => 'nullable|string|max:255',
            'is_indexed' => 'nullable|boolean',
            'is_followed' => 'nullable|boolean',
        ]);

        $type = $request->input('type');
        $id = $request->input('id');

        $updateData = $request->only([
            'meta_title', 'meta_description', 'meta_keywords',
            'og_title', 'og_description', 'twitter_title', 'twitter_description',
            'canonical_url', 'h1', 'h2', 'h3', 'is_indexed', 'is_followed'
        ]);

        if ($type === 'business') {
            $model = Business::findOrFail($id);
        } elseif ($type === 'category') {
            $model = Category::findOrFail($id);
        } elseif ($type === 'seo_page') {
            $model = SeoPage::findOrFail($id);
        }

        $model->update($updateData);

        return response()->json([
            'message' => 'SEO با موفقیت به‌روزرسانی شد',
            'data' => $model->fresh()
        ]);
    }

    /**
     * دریافت جزئیات SEO صفحه
     */
    public function getSeoPageDetails(Request $request): JsonResponse
    {
        $type = $request->input('type');
        $id = $request->input('id');

        if ($type === 'business') {
            $model = Business::with(['category', 'city', 'province'])->findOrFail($id);
        } elseif ($type === 'category') {
            $model = Category::findOrFail($id);
        } elseif ($type === 'seo_page') {
            $model = SeoPage::with(['category', 'city', 'neighborhood'])->findOrFail($id);
        }

        $seoData = [
            'id' => $model->id,
            'type' => $type,
            'name' => $model->name ?? $model->title,
            'url' => $this->generatePageUrl($model, $type),
            'meta_title' => $model->meta_title,
            'meta_description' => $model->meta_description,
            'meta_keywords' => $model->meta_keywords,
            'og_title' => $model->og_title,
            'og_description' => $model->og_description,
            'twitter_title' => $model->twitter_title,
            'twitter_description' => $model->twitter_description,
            'canonical_url' => $model->canonical_url,
            'h1' => $model->h1,
            'h2' => $model->h2 ?? null,
            'h3' => $model->h3 ?? null,
            'is_indexed' => $model->is_indexed ?? true,
            'is_followed' => $model->is_followed ?? true,
            'last_updated' => $model->updated_at,
        ];

        // اضافه کردن اطلاعات اضافی بر اساس نوع
        if ($type === 'business') {
            $seoData['business_info'] = [
                'category' => $model->category->name ?? null,
                'city' => $model->city->name ?? null,
                'province' => $model->province->name ?? null,
                'address' => $model->address,
                'phone' => $model->phone,
                'email' => $model->email,
                'website' => $model->website,
                'rating' => $model->rating,
                'reviews_count' => $model->reviews_count,
            ];
        } elseif ($type === 'category') {
            $seoData['category_info'] = [
                'description' => $model->description,
                'parent' => $model->parent->name ?? null,
                'businesses_count' => $model->businesses()->count(),
            ];
        }

        return response()->json($seoData);
    }

    /**
     * گزارش‌های SEO
     */
    public function getSeoReports(Request $request): JsonResponse
    {
        $reportType = $request->input('type', 'overview');

        switch ($reportType) {
            case 'overview':
                return $this->getOverviewReport();
            case 'missing_seo':
                return $this->getMissingSeoReport();
            case 'validation_errors':
                return $this->getValidationErrorsReport();
            case 'performance':
                return $this->getPerformanceReport();
            default:
                return $this->getOverviewReport();
        }
    }

    /**
     * گزارش کلی
     */
    private function getOverviewReport(): JsonResponse
    {
        $totalBusinesses = Business::count();
        $totalCategories = Category::count();
        $totalSeoPages = SeoPage::count();

        $businessesWithCompleteSeo = Business::whereNotNull('meta_title')
            ->whereNotNull('meta_description')
            ->whereNotNull('meta_keywords')
            ->count();

        $categoriesWithCompleteSeo = Category::whereNotNull('meta_title')
            ->whereNotNull('meta_description')
            ->whereNotNull('meta_keywords')
            ->count();

        return response()->json([
            'total_pages' => $totalBusinesses + $totalCategories + $totalSeoPages,
            'pages_with_complete_seo' => $businessesWithCompleteSeo + $categoriesWithCompleteSeo + $totalSeoPages,
            'seo_completion_percentage' => round((($businessesWithCompleteSeo + $categoriesWithCompleteSeo + $totalSeoPages) / ($totalBusinesses + $totalCategories + $totalSeoPages)) * 100, 2),
            'businesses' => [
                'total' => $totalBusinesses,
                'with_complete_seo' => $businessesWithCompleteSeo,
                'completion_percentage' => $totalBusinesses > 0 ? round(($businessesWithCompleteSeo / $totalBusinesses) * 100, 2) : 0,
            ],
            'categories' => [
                'total' => $totalCategories,
                'with_complete_seo' => $categoriesWithCompleteSeo,
                'completion_percentage' => $totalCategories > 0 ? round(($categoriesWithCompleteSeo / $totalCategories) * 100, 2) : 0,
            ],
        ]);
    }

    /**
     * گزارش صفحات بدون SEO
     */
    private function getMissingSeoReport(): JsonResponse
    {
        $businessesWithoutSeo = Business::where(function ($query) {
            $query->whereNull('meta_title')
                  ->orWhereNull('meta_description')
                  ->orWhereNull('meta_keywords');
        })->with(['category', 'city'])->get();

        $categoriesWithoutSeo = Category::where(function ($query) {
            $query->whereNull('meta_title')
                  ->orWhereNull('meta_description')
                  ->orWhereNull('meta_keywords');
        })->get();

        return response()->json([
            'businesses_without_seo' => $businessesWithoutSeo->map(function ($business) {
                return [
                    'id' => $business->id,
                    'name' => $business->name,
                    'category' => $business->category->name ?? null,
                    'city' => $business->city->name ?? null,
                    'missing_fields' => $this->getMissingSeoFields($business),
                ];
            }),
            'categories_without_seo' => $categoriesWithoutSeo->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'missing_fields' => $this->getMissingCategorySeoFields($category),
                ];
            }),
        ]);
    }

    /**
     * گزارش خطاهای اعتبارسنجی
     */
    private function getValidationErrorsReport(): JsonResponse
    {
        $businesses = Business::all();
        $categories = Category::all();

        $businessErrors = [];
        $categoryErrors = [];

        foreach ($businesses as $business) {
            $errors = $this->validateBusinessSeoErrors($business);
            if (!empty($errors)) {
                $businessErrors[] = [
                    'id' => $business->id,
                    'name' => $business->name,
                    'errors' => $errors,
                ];
            }
        }

        foreach ($categories as $category) {
            $errors = $this->validateCategorySeoErrors($category);
            if (!empty($errors)) {
                $categoryErrors[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'errors' => $errors,
                ];
            }
        }

        return response()->json([
            'business_errors' => $businessErrors,
            'category_errors' => $categoryErrors,
            'total_errors' => count($businessErrors) + count($categoryErrors),
        ]);
    }

    /**
     * گزارش عملکرد
     */
    private function getPerformanceReport(): JsonResponse
    {
        $lastWeek = now()->subWeek();
        $lastMonth = now()->subMonth();

        return response()->json([
            'seo_updates_last_week' => Business::where('updated_at', '>=', $lastWeek)
                ->where(function ($query) {
                    $query->whereNotNull('meta_title')
                          ->orWhereNotNull('meta_description')
                          ->orWhereNotNull('meta_keywords');
                })->count(),
            'seo_updates_last_month' => Business::where('updated_at', '>=', $lastMonth)
                ->where(function ($query) {
                    $query->whereNotNull('meta_title')
                          ->orWhereNotNull('meta_description')
                          ->orWhereNotNull('meta_keywords');
                })->count(),
            'sitemap_last_generated' => Storage::disk('public')->exists('sitemap.xml') 
                ? Storage::disk('public')->lastModified('sitemap.xml') 
                : null,
            'robots_last_generated' => Storage::disk('public')->exists('robots.txt') 
                ? Storage::disk('public')->lastModified('robots.txt') 
                : null,
        ]);
    }

    /**
     * محاسبه وضعیت SEO کسب‌وکار
     */
    private function calculateSeoStatus(Business $business): string
    {
        $hasTitle = !empty($business->meta_title);
        $hasDescription = !empty($business->meta_description);
        $hasKeywords = !empty($business->meta_keywords);
        $hasAddress = !empty($business->address);
        $hasPhone = !empty($business->phone);

        $score = 0;
        if ($hasTitle) $score += 20;
        if ($hasDescription) $score += 20;
        if ($hasKeywords) $score += 20;
        if ($hasAddress) $score += 20;
        if ($hasPhone) $score += 20;

        if ($score >= 80) return 'complete';
        if ($score >= 40) return 'incomplete';
        return 'missing';
    }

    /**
     * محاسبه وضعیت SEO دسته‌بندی
     */
    private function calculateCategorySeoStatus(Category $category): string
    {
        $hasTitle = !empty($category->meta_title);
        $hasDescription = !empty($category->meta_description);
        $hasKeywords = !empty($category->meta_keywords);
        $hasH1 = !empty($category->h1);

        $score = 0;
        if ($hasTitle) $score += 25;
        if ($hasDescription) $score += 25;
        if ($hasKeywords) $score += 25;
        if ($hasH1) $score += 25;

        if ($score >= 80) return 'complete';
        if ($score >= 40) return 'incomplete';
        return 'missing';
    }

    /**
     * محاسبه وضعیت SEO صفحه سفارشی
     */
    private function calculateSeoPageStatus(SeoPage $seoPage): string
    {
        $hasTitle = !empty($seoPage->title);
        $hasDescription = !empty($seoPage->meta_description);
        $hasKeywords = !empty($seoPage->keywords);
        $hasH1 = !empty($seoPage->h1);

        $score = 0;
        if ($hasTitle) $score += 25;
        if ($hasDescription) $score += 25;
        if ($hasKeywords) $score += 25;
        if ($hasH1) $score += 25;

        if ($score >= 80) return 'complete';
        if ($score >= 40) return 'incomplete';
        return 'missing';
    }

    /**
     * تولید URL برای صفحه
     */
    private function generatePageUrl($model, string $type): string
    {
        if ($type === 'business') {
            return "/business/{$model->slug}";
        } elseif ($type === 'category') {
            return "/b/{$model->slug}";
        } elseif ($type === 'seo_page') {
            return $this->generateSeoPageUrl($model);
        }
        return '/';
    }

    /**
     * تولید URL برای صفحه SEO سفارشی
     */
    private function generateSeoPageUrl(SeoPage $seoPage): string
    {
        if ($seoPage->neighborhood) {
            return "/b/{$seoPage->category->slug}/{$seoPage->city->slug}/{$seoPage->neighborhood->slug}";
        } elseif ($seoPage->city) {
            return "/b/{$seoPage->category->slug}/{$seoPage->city->slug}";
        } else {
            return "/b/{$seoPage->category->slug}";
        }
    }

    /**
     * تولید رشته مکان
     */
    private function generateLocationString(SeoPage $seoPage): string
    {
        $location = [];
        if ($seoPage->neighborhood) {
            $location[] = $seoPage->neighborhood->name;
        }
        if ($seoPage->city) {
            $location[] = $seoPage->city->name;
        }
        return implode('، ', $location);
    }

    /**
     * دریافت فیلدهای ناقص SEO کسب‌وکار
     */
    private function getMissingSeoFields(Business $business): array
    {
        $missing = [];
        if (empty($business->meta_title)) $missing[] = 'meta_title';
        if (empty($business->meta_description)) $missing[] = 'meta_description';
        if (empty($business->meta_keywords)) $missing[] = 'meta_keywords';
        if (empty($business->address)) $missing[] = 'address';
        if (empty($business->phone)) $missing[] = 'phone';
        return $missing;
    }

    /**
     * دریافت فیلدهای ناقص SEO دسته‌بندی
     */
    private function getMissingCategorySeoFields(Category $category): array
    {
        $missing = [];
        if (empty($category->meta_title)) $missing[] = 'meta_title';
        if (empty($category->meta_description)) $missing[] = 'meta_description';
        if (empty($category->meta_keywords)) $missing[] = 'meta_keywords';
        if (empty($category->h1)) $missing[] = 'h1';
        return $missing;
    }

    /**
     * اعتبارسنجی خطاهای SEO کسب‌وکار
     */
    private function validateBusinessSeoErrors(Business $business): array
    {
        $errors = [];
        
        if (!empty($business->meta_title) && strlen($business->meta_title) > 60) {
            $errors[] = 'Meta title بیش از 60 کاراکتر است';
        }
        
        if (!empty($business->meta_description) && strlen($business->meta_description) > 160) {
            $errors[] = 'Meta description بیش از 160 کاراکتر است';
        }
        
        if (!empty($business->meta_description) && strlen($business->meta_description) < 120) {
            $errors[] = 'Meta description کمتر از 120 کاراکتر است';
        }

        return $errors;
    }

    /**
     * اعتبارسنجی خطاهای SEO دسته‌بندی
     */
    private function validateCategorySeoErrors(Category $category): array
    {
        $errors = [];
        
        if (!empty($category->meta_title) && strlen($category->meta_title) > 60) {
            $errors[] = 'Meta title بیش از 60 کاراکتر است';
        }
        
        if (!empty($category->meta_description) && strlen($category->meta_description) > 160) {
            $errors[] = 'Meta description بیش از 160 کاراکتر است';
        }
        
        if (!empty($category->meta_description) && strlen($category->meta_description) < 120) {
            $errors[] = 'Meta description کمتر از 120 کاراکتر است';
        }

        return $errors;
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
}
