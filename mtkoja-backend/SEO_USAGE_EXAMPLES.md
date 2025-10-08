# مثال‌های استفاده از سیستم SEO متکوجا

## 🚀 شروع سریع

### 1. دریافت اطلاعات SEO برای صفحه کسب‌وکار

```bash
GET /api/business/cafe-x-tehran
```

**پاسخ:**
```json
{
  "business": {
    "id": 1,
    "name": "کافه X",
    "slug": "cafe-x-tehran",
    "description": "بهترین کافه در تهران با فضای دنج و قهوه‌های عالی",
    "category": {
      "name": "کافه",
      "slug": "cafe"
    },
    "city": {
      "name": "تهران",
      "slug": "tehran"
    }
  },
  "seo": {
    "title": "کافه X - کافه در تهران",
    "description": "کافه X در تهران. بهترین کافه با فضای دنج و قهوه‌های عالی - تماس: 02112345678",
    "keywords": "کافه X, کافه, تهران, کسب و کار, امتیاز, نظرات",
    "canonical": "https://mtkoja.com/business/cafe-x-tehran",
    "h1": "کافه X",
    "og:title": "کافه X - کافه در تهران",
    "og:description": "کافه X در تهران. بهترین کافه با فضای دنج و قهوه‌های عالی",
    "og:image": "https://mtkoja.com/images/cafe-x.jpg",
    "og:url": "https://mtkoja.com/business/cafe-x-tehran",
    "og:type": "business.business",
    "twitter:card": "summary_large_image",
    "twitter:title": "کافه X - کافه در تهران",
    "twitter:description": "کافه X در تهران. بهترین کافه با فضای دنج و قهوه‌های عالی"
  },
  "breadcrumbs": {
    "items": [
      {"name": "خانه", "url": "/"},
      {"name": "کافه", "url": "/b/cafe"},
      {"name": "تهران", "url": "/b/cafe/tehran"},
      {"name": "کافه X", "url": "/business/cafe-x-tehran"}
    ],
    "schema": {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [...]
    }
  },
  "json_ld": [
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "کافه X",
      "description": "بهترین کافه در تهران با فضای دنج و قهوه‌های عالی",
      "url": "https://mtkoja.com/business/cafe-x-tehran",
      "telephone": "02112345678",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "خیابان ولیعصر، پلاک 123",
        "addressLocality": "تهران",
        "addressRegion": "تهران",
        "addressCountry": "IR"
      },
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": 4.5,
        "reviewCount": 120
      }
    }
  ]
}
```

### 2. دریافت اطلاعات SEO برای صفحه دسته‌بندی

```bash
GET /api/b/cafe/tehran
```

**پاسخ:**
```json
{
  "businesses": [...],
  "category": {
    "name": "کافه",
    "slug": "cafe"
  },
  "city": {
    "name": "تهران",
    "slug": "tehran"
  },
  "seo": {
    "title": "بهترین کافه در تهران",
    "description": "لیست کامل کافه در تهران. بهترین کافه با امتیاز و نظرات کاربران در تهران.",
    "keywords": "کافه, تهران, کسب و کار, امتیاز, نظرات, بهترین",
    "canonical": "https://mtkoja.com/b/cafe/tehran",
    "h1": "بهترین کافه در تهران"
  },
  "breadcrumbs": [
    {"name": "خانه", "url": "/"},
    {"name": "کافه", "url": "/b/cafe"},
    {"name": "تهران", "url": "/b/cafe/tehran"}
  ],
  "json_ld": {
    "@context": "https://schema.org",
    "@type": "ItemList",
    "name": "کافه در تهران",
    "description": "لیست کافه در تهران",
    "url": "https://mtkoja.com/b/cafe/tehran",
    "numberOfItems": 45,
    "itemListElement": [...]
  }
}
```

## 🛠 استفاده در پنل ادمین

### 1. داشبورد SEO

```bash
GET /api/admin/seo/dashboard
```

**پاسخ:**
```json
{
  "total_businesses": 1500,
  "businesses_with_seo": 1200,
  "total_categories": 25,
  "categories_with_seo": 20,
  "seo_pages_count": 50,
  "indexed_businesses": 1100,
  "indexed_categories": 18,
  "last_sitemap_update": "2024-12-26 10:30:00"
}
```

### 2. تولید خودکار SEO برای کسب‌وکار

```bash
POST /api/admin/seo/generate-business/1
```

**پاسخ:**
```json
{
  "message": "SEO برای کسب‌وکار با موفقیت تولید شد",
  "business": {
    "id": 1,
    "meta_title": "کافه X - کافه در تهران",
    "meta_description": "کافه X در تهران. بهترین کافه با فضای دنج و قهوه‌های عالی - تماس: 02112345678",
    "meta_keywords": "کافه X, کافه, تهران, کسب و کار, امتیاز, نظرات"
  }
}
```

### 3. تولید انبوه SEO

```bash
POST /api/admin/seo/generate-bulk
Content-Type: application/json

{
  "type": "businesses",
  "ids": [1, 2, 3, 4, 5]
}
```

**پاسخ:**
```json
{
  "message": "SEO برای 5 مورد با موفقیت تولید شد"
}
```

### 4. اعتبارسنجی SEO

```bash
POST /api/admin/seo/validate
Content-Type: application/json

{
  "type": "business",
  "id": 1
}
```

**پاسخ:**
```json
{
  "type": "business",
  "id": 1,
  "issues": [],
  "score": 95,
  "recommendations": [
    "اضافه کردن لینک‌های شبکه‌های اجتماعی",
    "تعریف ساعت کاری کسب‌وکار"
  ]
}
```

## 🔧 استفاده از سرویس SEO در کد

### 1. تولید Meta Title

```php
use App\Services\SeoService;

$seoService = new SeoService();

// برای کسب‌وکار
$business = Business::find(1);
$title = $seoService->generateBusinessMetaTitle($business);
// نتیجه: "کافه X - کافه در تهران"

// برای دسته‌بندی
$category = Category::find(1);
$city = City::find(1);
$title = $seoService->generateCategoryMetaTitle($category, $city);
// نتیجه: "بهترین کافه در تهران"
```

### 2. تولید Open Graph Tags

```php
$seoData = [
    'title' => 'کافه X - کافه در تهران',
    'description' => 'بهترین کافه در تهران',
    'canonical' => 'https://mtkoja.com/business/cafe-x',
    'og_type' => 'business.business',
    'og_image' => 'https://mtkoja.com/images/cafe-x.jpg'
];

$ogTags = $seoService->generateOpenGraphTags($seoData);
// نتیجه:
// [
//     'og:title' => 'کافه X - کافه در تهران',
//     'og:description' => 'بهترین کافه در تهران',
//     'og:type' => 'business.business',
//     'og:url' => 'https://mtkoja.com/business/cafe-x',
//     'og:image' => 'https://mtkoja.com/images/cafe-x.jpg',
//     'og:site_name' => 'متکوجا',
//     'og:locale' => 'fa_IR'
// ]
```

### 3. تولید Schema Markup

```php
$business = Business::with(['category', 'city', 'province'])->find(1);
$schema = $seoService->generateCompleteStructuredData($business);
// نتیجه: آرایه‌ای از Schema Markup های مختلف
```

## 📊 تولید Sitemap و Robots.txt

### 1. تولید Sitemap

```bash
GET /api/seo/sitemap/generate
```

**پاسخ:**
```json
{
  "message": "Sitemap با موفقیت تولید شد",
  "url": "https://mtkoja.com/storage/sitemap.xml"
}
```

### 2. تولید Robots.txt

```bash
GET /api/seo/robots/generate
```

**پاسخ:**
```json
{
  "message": "robots.txt با موفقیت تولید شد",
  "url": "https://mtkoja.com/storage/robots.txt"
}
```

## 🎨 استفاده در Frontend

### 1. Blade Template

```blade
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Basic Meta Tags -->
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">
    <meta name="keywords" content="{{ $seo['keywords'] }}">
    <link rel="canonical" href="{{ $seo['canonical'] }}">
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="{{ $seo['og:title'] }}">
    <meta property="og:description" content="{{ $seo['og:description'] }}">
    <meta property="og:image" content="{{ $seo['og:image'] }}">
    <meta property="og:url" content="{{ $seo['og:url'] }}">
    <meta property="og:type" content="{{ $seo['og:type'] }}">
    <meta property="og:site_name" content="{{ $seo['og:site_name'] }}">
    <meta property="og:locale" content="{{ $seo['og:locale'] }}">
    
    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="{{ $seo['twitter:card'] }}">
    <meta name="twitter:title" content="{{ $seo['twitter:title'] }}">
    <meta name="twitter:description" content="{{ $seo['twitter:description'] }}">
    <meta name="twitter:image" content="{{ $seo['twitter:image'] }}">
    <meta name="twitter:site" content="{{ $seo['twitter:site'] }}">
    <meta name="twitter:creator" content="{{ $seo['twitter:creator'] }}">
    
    <!-- Schema Markup -->
    @if(isset($seo['json_ld']))
        @foreach($seo['json_ld'] as $schema)
            <script type="application/ld+json">
                {!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
            </script>
        @endforeach
    @endif
</head>
<body>
    <!-- Breadcrumbs -->
    @if(isset($breadcrumbs['items']))
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                @foreach($breadcrumbs['items'] as $breadcrumb)
                    <li class="breadcrumb-item">
                        <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a>
                    </li>
                @endforeach
            </ol>
        </nav>
    @endif
    
    <!-- Main Content -->
    <main>
        <h1>{{ $seo['h1'] }}</h1>
        <!-- محتوای صفحه -->
    </main>
</body>
</html>
```

### 2. Vue.js Component

```vue
<template>
  <div>
    <!-- SEO Head -->
    <Head>
      <title>{{ seo.title }}</title>
      <meta name="description" :content="seo.description" />
      <meta name="keywords" :content="seo.keywords" />
      <link rel="canonical" :href="seo.canonical" />
      
      <!-- Open Graph -->
      <meta property="og:title" :content="seo['og:title']" />
      <meta property="og:description" :content="seo['og:description']" />
      <meta property="og:image" :content="seo['og:image']" />
      <meta property="og:url" :content="seo['og:url']" />
      <meta property="og:type" :content="seo['og:type']" />
      
      <!-- Twitter Card -->
      <meta name="twitter:card" :content="seo['twitter:card']" />
      <meta name="twitter:title" :content="seo['twitter:title']" />
      <meta name="twitter:description" :content="seo['twitter:description']" />
      <meta name="twitter:image" :content="seo['twitter:image']" />
    </Head>
    
    <!-- Breadcrumbs -->
    <nav v-if="breadcrumbs.items" class="breadcrumb">
      <ol>
        <li v-for="(item, index) in breadcrumbs.items" :key="index">
          <a :href="item.url">{{ item.name }}</a>
        </li>
      </ol>
    </nav>
    
    <!-- Main Content -->
    <main>
      <h1>{{ seo.h1 }}</h1>
      <!-- محتوای صفحه -->
    </main>
  </div>
</template>

<script>
export default {
  props: {
    seo: Object,
    breadcrumbs: Object
  }
}
</script>
```

## 🔍 تست و اعتبارسنجی

### 1. تست Meta Tags

```bash
# تست با curl
curl -H "Accept: application/json" https://mtkoja.com/api/business/cafe-x-tehran

# تست با Postman
GET https://mtkoja.com/api/business/cafe-x-tehran
```

### 2. تست Schema Markup

از ابزارهای زیر استفاده کنید:
- [Google Rich Results Test](https://search.google.com/test/rich-results)
- [Schema.org Validator](https://validator.schema.org/)
- [JSON-LD Playground](https://json-ld.org/playground/)

### 3. تست Open Graph

از ابزارهای زیر استفاده کنید:
- [Facebook Sharing Debugger](https://developers.facebook.com/tools/debug/)
- [Twitter Card Validator](https://cards-dev.twitter.com/validator)
- [LinkedIn Post Inspector](https://www.linkedin.com/post-inspector/)

## 📈 مانیتورینگ و آنالیتیکس

### 1. Google Search Console

- اضافه کردن Sitemap
- مانیتورینگ خطاهای crawl
- بررسی عملکرد صفحات

### 2. Google Analytics

- ردیابی ترافیک ارگانیک
- تحلیل کلمات کلیدی
- بررسی صفحات محبوب

### 3. ابزارهای SEO

- [Screaming Frog](https://www.screamingfrog.co.uk/seo-spider/)
- [Ahrefs](https://ahrefs.com/)
- [SEMrush](https://www.semrush.com/)

## 🎯 بهترین روش‌ها

### 1. کلمات کلیدی
- استفاده از کلمات کلیدی طولانی (Long-tail keywords)
- تمرکز بر کلمات کلیدی محلی
- اجتناب از تکرار بیش از حد

### 2. محتوا
- تولید محتوای باکیفیت و منحصر به فرد
- استفاده از تصاویر با Alt Text مناسب
- ساختار مناسب H1, H2, H3

### 3. فنی
- سرعت لود بالا
- طراحی ریسپانسیو
- URL های SEO-friendly

## 🔧 شناسه تغییرات

**شناسه تغییرات فعلی**: `SEO_EXAMPLES_v1.0`

در صورت بروز مشکل، از این شناسه برای بازگشت به حالت قبلی استفاده کنید.

