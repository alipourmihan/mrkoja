<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Category;
use App\Models\Province;
use App\Models\City;
use App\Models\Neighborhood;
use App\Models\SeoPage;
use Illuminate\Support\Str;

class SeoService
{
    /**
     * تولید Meta Title برای کسب‌وکار
     */
    public function generateBusinessMetaTitle(Business $business): string
    {
        if ($business->meta_title) {
            return $business->meta_title;
        }

        $title = "{$business->name} - {$business->category->name}";
        
        if ($business->city) {
            $title .= " در {$business->city->name}";
        }

        // محدود کردن به 60 کاراکتر
        return Str::limit($title, 60, '');
    }

    /**
     * تولید Meta Description برای کسب‌وکار
     */
    public function generateBusinessMetaDescription(Business $business): string
    {
        if ($business->meta_description) {
            return $business->meta_description;
        }

        $description = $business->description;
        
        if ($business->city) {
            $description .= " در {$business->city->name}";
        }

        if ($business->phone) {
            $description .= " - تماس: {$business->phone}";
        }

        // محدود کردن به 160 کاراکتر
        return Str::limit($description, 160, '');
    }

    /**
     * تولید Meta Title برای دسته‌بندی
     */
    public function generateCategoryMetaTitle(Category $category, ?City $city = null, ?Neighborhood $neighborhood = null): string
    {
        $title = "بهترین {$category->name}";
        
        if ($neighborhood) {
            $title .= " در {$neighborhood->name}، {$city->name}";
        } elseif ($city) {
            $title .= " در {$city->name}";
        } else {
            $title .= " در ایران";
        }

        return Str::limit($title, 60, '');
    }

    /**
     * تولید Meta Description برای دسته‌بندی
     */
    public function generateCategoryMetaDescription(Category $category, ?City $city = null, ?Neighborhood $neighborhood = null): string
    {
        $description = "لیست کامل {$category->name}";
        
        if ($neighborhood) {
            $description .= " در محله {$neighborhood->name}، {$city->name}";
        } elseif ($city) {
            $description .= " در {$city->name}";
        } else {
            $description .= " در سراسر ایران";
        }

        $description .= ". بهترین {$category->name} با امتیاز و نظرات کاربران";

        return Str::limit($description, 160, '');
    }

    /**
     * تولید کلمات کلیدی برای کسب‌وکار
     */
    public function generateBusinessKeywords(Business $business): string
    {
        if ($business->meta_keywords) {
            return $business->meta_keywords;
        }

        $keywords = [
            $business->name,
            $business->category->name
        ];

        if ($business->city) {
            $keywords[] = $business->city->name;
        }

        if ($business->neighborhood) {
            $keywords[] = $business->neighborhood->name;
        }

        $keywords[] = 'کسب و کار';
        $keywords[] = 'امتیاز';
        $keywords[] = 'نظرات';

        return implode(', ', array_unique($keywords));
    }

    /**
     * تولید کلمات کلیدی برای دسته‌بندی
     */
    public function generateCategoryKeywords(Category $category, ?City $city = null, ?Neighborhood $neighborhood = null): string
    {
        $keywords = [$category->name];

        if ($neighborhood) {
            $keywords[] = $neighborhood->name;
        }

        if ($city) {
            $keywords[] = $city->name;
        }

        $keywords[] = 'کسب و کار';
        $keywords[] = 'امتیاز';
        $keywords[] = 'نظرات';
        $keywords[] = 'بهترین';

        return implode(', ', array_unique($keywords));
    }

    /**
     * تولید Open Graph Tags
     */
    public function generateOpenGraphTags(array $seoData): array
    {
        return [
            'og:title' => $seoData['title'],
            'og:description' => $seoData['description'],
            'og:type' => $seoData['og_type'] ?? 'website',
            'og:url' => $seoData['canonical'],
            'og:image' => $seoData['og_image'] ?? url('images/default-og-image.jpg'),
            'og:site_name' => 'متکوجا',
            'og:locale' => 'fa_IR'
        ];
    }

    /**
     * تولید Twitter Card Tags
     */
    public function generateTwitterCardTags(array $seoData): array
    {
        return [
            'twitter:card' => 'summary_large_image',
            'twitter:title' => $seoData['title'],
            'twitter:description' => $seoData['description'],
            'twitter:image' => $seoData['og_image'] ?? url('images/default-og-image.jpg'),
            'twitter:site' => '@mtkoja',
            'twitter:creator' => '@mtkoja'
        ];
    }

    /**
     * تولید Breadcrumb Schema
     */
    public function generateBreadcrumbSchema(array $breadcrumbs): array
    {
        $items = [];
        
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['name'],
                'item' => url($breadcrumb['url'])
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items
        ];
    }

    /**
     * تولید FAQ Schema
     */
    public function generateFaqSchema(array $faqs): array
    {
        $items = [];
        
        foreach ($faqs as $faq) {
            $items[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer']
                ]
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $items
        ];
    }

    /**
     * تولید Article Schema
     */
    public function generateArticleSchema(array $articleData): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $articleData['title'],
            'description' => $articleData['description'],
            'image' => $articleData['image'] ?? url('images/default-article.jpg'),
            'author' => [
                '@type' => 'Organization',
                'name' => 'متکوجا'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'متکوجا',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => url('images/logo.png')
                ]
            ],
            'datePublished' => $articleData['published_at'] ?? now()->toISOString(),
            'dateModified' => $articleData['updated_at'] ?? now()->toISOString()
        ];
    }

    /**
     * تولید URL SEO-friendly
     */
    public function generateSeoUrl(string $type, array $params): string
    {
        switch ($type) {
            case 'business':
                return "/business/{$params['slug']}";
            
            case 'category':
                return "/b/{$params['category_slug']}";
            
            case 'category_city':
                return "/b/{$params['category_slug']}/{$params['city_slug']}";
            
            case 'category_city_neighborhood':
                return "/b/{$params['category_slug']}/{$params['city_slug']}/{$params['neighborhood_slug']}";
            
            default:
                return '/';
        }
    }

    /**
     * اعتبارسنجی Meta Title
     */
    public function validateMetaTitle(string $title): array
    {
        $issues = [];
        
        if (empty($title)) {
            $issues[] = 'عنوان خالی است';
        } elseif (strlen($title) > 60) {
            $issues[] = 'عنوان بیش از 60 کاراکتر است';
        } elseif (strlen($title) < 30) {
            $issues[] = 'عنوان کمتر از 30 کاراکتر است';
        }

        return $issues;
    }

    /**
     * اعتبارسنجی Meta Description
     */
    public function validateMetaDescription(string $description): array
    {
        $issues = [];
        
        if (empty($description)) {
            $issues[] = 'توضیحات خالی است';
        } elseif (strlen($description) > 160) {
            $issues[] = 'توضیحات بیش از 160 کاراکتر است';
        } elseif (strlen($description) < 120) {
            $issues[] = 'توضیحات کمتر از 120 کاراکتر است';
        }

        return $issues;
    }

    /**
     * تولید Alt Text برای تصاویر
     */
    public function generateImageAltText(Business $business, int $imageIndex = 0): string
    {
        $alt = $business->name;
        
        if ($business->category) {
            $alt .= " - {$business->category->name}";
        }
        
        if ($business->city) {
            $alt .= " در {$business->city->name}";
        }

        if ($imageIndex > 0) {
            $alt .= " - تصویر " . ($imageIndex + 1);
        }

        return $alt;
    }

    /**
     * تولید Structured Data کامل
     */
    public function generateCompleteStructuredData(Business $business): array
    {
        $structuredData = [];

        // LocalBusiness Schema
        $structuredData[] = $this->generateBusinessSchema($business);

        // Breadcrumb Schema
        $breadcrumbs = [
            ['name' => 'خانه', 'url' => '/'],
            ['name' => $business->category->name, 'url' => "/b/{$business->category->slug}"],
            ['name' => $business->city->name, 'url' => "/b/{$business->category->slug}/{$business->city->slug}"],
            ['name' => $business->name, 'url' => "/business/{$business->slug}"]
        ];
        $structuredData[] = $this->generateBreadcrumbSchema($breadcrumbs);

        return $structuredData;
    }

    /**
     * تولید Business Schema
     */
    private function generateBusinessSchema(Business $business): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $business->name,
            'description' => $business->description,
            'url' => url("/business/{$business->slug}"),
            'telephone' => $business->phone,
            'email' => $business->email,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $business->address,
                'addressLocality' => $business->city->name,
                'addressRegion' => $business->province->name,
                'postalCode' => $business->postal_code ?? '',
                'addressCountry' => 'IR'
            ]
        ];

        if ($business->latitude && $business->longitude) {
            $schema['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => $business->latitude,
                'longitude' => $business->longitude
            ];
        }

        if ($business->rating > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $business->rating,
                'reviewCount' => $business->reviews_count
            ];
        }

        if ($business->working_hours) {
            $schema['openingHours'] = $this->formatWorkingHours($business->working_hours);
        }

        return $schema;
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

