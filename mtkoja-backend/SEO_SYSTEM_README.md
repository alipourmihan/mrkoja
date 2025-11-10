# سیستم SEO متکوجا

## 🎯 معرفی

سیستم SEO متکوجا یک سیستم جامع برای بهینه‌سازی موتورهای جستجو است که شامل تمامی المان‌های ضروری SEO می‌باشد.

## 🚀 ویژگی‌های اصلی

### 1. Meta Tags کامل
- **Meta Title**: حداکثر 60 کاراکتر، شامل کلمه کلیدی اصلی
- **Meta Description**: بین 120 تا 160 کاراکتر، توضیح جذاب
- **Meta Keywords**: کلمات کلیدی مرتبط
- **Canonical URL**: جلوگیری از محتوای تکراری

### 2. Open Graph Tags
- `og:title` - عنوان برای شبکه‌های اجتماعی
- `og:description` - توضیحات برای شبکه‌های اجتماعی
- `og:image` - تصویر برای اشتراک‌گذاری
- `og:url` - آدرس صفحه
- `og:type` - نوع محتوا
- `og:site_name` - نام سایت
- `og:locale` - زبان سایت

### 3. Twitter Card Tags
- `twitter:card` - نوع کارت توییتر
- `twitter:title` - عنوان توییتر
- `twitter:description` - توضیحات توییتر
- `twitter:image` - تصویر توییتر
- `twitter:site` - اکانت توییتر سایت

### 4. Schema Markup (Structured Data)
- **LocalBusiness**: برای کسب‌وکارها
- **ItemList**: برای صفحات دسته‌بندی
- **WebSite**: برای صفحه اصلی
- **BreadcrumbList**: برای مسیر صفحات
- **FAQPage**: برای سوالات متداول
- **Article**: برای مقالات

### 5. URL Structure
- `/b/{category}` - صفحات دسته‌بندی
- `/b/{category}/{city}` - دسته‌بندی + شهر
- `/b/{category}/{city}/{neighborhood}` - دسته‌بندی + شهر + محله
- `/business/{business}` - پروفایل کسب‌وکار

## 📁 ساختار فایل‌ها

```
app/
├── Http/Controllers/
│   ├── SeoController.php              # کنترلر اصلی SEO
│   ├── SeoPageController.php          # مدیریت صفحات SEO
│   ├── SeoManagerController.php       # مدیریت فنی SEO
│   └── Admin/SeoAdminController.php   # پنل ادمین SEO
├── Services/
│   └── SeoService.php                 # سرویس SEO
└── Models/
    ├── SeoPage.php                    # مدل صفحات SEO
    ├── Business.php                   # مدل کسب‌وکار (به‌روزرسانی شده)
    └── Category.php                   # مدل دسته‌بندی (به‌روزرسانی شده)
```

## 🛠 API Endpoints

### عمومی
- `GET /b/{category}` - صفحه دسته‌بندی
- `GET /b/{category}/{city}` - دسته‌بندی + شهر
- `GET /b/{category}/{city}/{neighborhood}` - دسته‌بندی + شهر + محله
- `GET /business/{business}` - پروفایل کسب‌وکار

### مدیریت SEO
- `GET /seo/sitemap/generate` - تولید Sitemap
- `GET /seo/robots/generate` - تولید robots.txt
- `GET /seo/homepage/schema` - Schema صفحه اصلی
- `POST /seo/validate` - اعتبارسنجی SEO
- `GET /seo/stats` - آمار SEO

### پنل ادمین
- `GET /admin/seo/dashboard` - داشبورد SEO
- `GET /admin/seo/businesses-without-seo` - کسب‌وکارهای بدون SEO
- `GET /admin/seo/categories-without-seo` - دسته‌بندی‌های بدون SEO
- `POST /admin/seo/generate-business/{business}` - تولید SEO کسب‌وکار
- `POST /admin/seo/generate-category/{category}` - تولید SEO دسته‌بندی
- `POST /admin/seo/generate-bulk` - تولید انبوه SEO
- `POST /admin/seo/validate` - اعتبارسنجی SEO
- `POST /admin/seo/generate-sitemap` - تولید Sitemap
- `POST /admin/seo/generate-robots` - تولید robots.txt

## 📊 انواع صفحات SEO

### 1. صفحه اصلی
```json
{
  "title": "متکوجا - راهنمای کسب‌وکارهای محلی",
  "description": "پلتفرم جامع برای یافتن بهترین کسب‌وکارهای محلی در سراسر ایران",
  "schema": "WebSite + SearchAction"
}
```

### 2. صفحات دسته‌بندی
```json
{
  "title": "بهترین کافه در تهران",
  "description": "لیست کامل کافه در تهران. بهترین کافه با امتیاز و نظرات کاربران در تهران.",
  "schema": "ItemList"
}
```

### 3. صفحات کسب‌وکار
```json
{
  "title": "کافه X - کافه در تهران",
  "description": "کافه X در تهران. بهترین کافه با امتیاز 4.5 و 120 نظر کاربران.",
  "schema": "LocalBusiness + BreadcrumbList"
}
```

## 🔧 استفاده از سرویس SEO

```php
use App\Services\SeoService;

$seoService = new SeoService();

// تولید Meta Title برای کسب‌وکار
$title = $seoService->generateBusinessMetaTitle($business);

// تولید Meta Description برای دسته‌بندی
$description = $seoService->generateCategoryMetaDescription($category, $city);

// تولید Open Graph Tags
$ogTags = $seoService->generateOpenGraphTags($seoData);

// تولید Schema Markup
$schema = $seoService->generateBusinessSchema($business);
```

## 📈 اعتبارسنجی SEO

سیستم شامل اعتبارسنجی کامل SEO است:

### کسب‌وکارها
- ✅ Meta Title (30-60 کاراکتر)
- ✅ Meta Description (120-160 کاراکتر)
- ✅ کلمات کلیدی
- ✅ آدرس کامل
- ✅ شماره تماس
- ✅ تصاویر
- ✅ ساعت کاری
- ✅ شبکه‌های اجتماعی

### دسته‌بندی‌ها
- ✅ Meta Title (30-60 کاراکتر)
- ✅ Meta Description (120-160 کاراکتر)
- ✅ کلمات کلیدی
- ✅ توضیحات دسته‌بندی
- ✅ تعداد کسب‌وکارهای فعال
- ✅ عنوان H1

## 🗺 Sitemap و Robots.txt

### Sitemap XML
- صفحه اصلی (اولویت: 1.0)
- صفحات دسته‌بندی (اولویت: 0.8)
- صفحات کسب‌وکار (اولویت: 0.6)

### Robots.txt
```
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /api/
Disallow: /storage/private/

Sitemap: https://example.com/storage/sitemap.xml
```

## 🎨 Frontend Integration

### HTML Head
```html
<head>
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">
    <meta name="keywords" content="{{ $seo['keywords'] }}">
    <link rel="canonical" href="{{ $seo['canonical'] }}">
    
    <!-- Open Graph -->
    <meta property="og:title" content="{{ $seo['og:title'] }}">
    <meta property="og:description" content="{{ $seo['og:description'] }}">
    <meta property="og:image" content="{{ $seo['og:image'] }}">
    <meta property="og:url" content="{{ $seo['og:url'] }}">
    <meta property="og:type" content="{{ $seo['og:type'] }}">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="{{ $seo['twitter:card'] }}">
    <meta name="twitter:title" content="{{ $seo['twitter:title'] }}">
    <meta name="twitter:description" content="{{ $seo['twitter:description'] }}">
    <meta name="twitter:image" content="{{ $seo['twitter:image'] }}">
    
    <!-- Schema Markup -->
    <script type="application/ld+json">
    {!! json_encode($seo['json_ld'], JSON_UNESCAPED_UNICODE) !!}
    </script>
</head>
```

## 📋 چک‌لیست SEO

### ✅ فنی
- [ ] Sitemap XML
- [ ] Robots.txt
- [ ] Canonical URLs
- [ ] Mobile Friendly
- [ ] Page Speed
- [ ] SSL Certificate

### ✅ محتوا
- [ ] Meta Title (30-60 کاراکتر)
- [ ] Meta Description (120-160 کاراکتر)
- [ ] H1 Tag (یکتا در هر صفحه)
- [ ] Alt Text برای تصاویر
- [ ] کلمات کلیدی مناسب

### ✅ Schema Markup
- [ ] LocalBusiness برای کسب‌وکارها
- [ ] ItemList برای دسته‌بندی‌ها
- [ ] BreadcrumbList
- [ ] WebSite برای صفحه اصلی

## 🔄 Migration

برای اعمال تغییرات دیتابیس:

```bash
php artisan migrate
```

## 📝 شناسه تغییرات

**شناسه تغییرات فعلی**: `SEO_SYSTEM_v1.0`

در صورت بروز مشکل، از این شناسه برای بازگشت به حالت قبلی استفاده کنید.

## 🎯 نتیجه

این سیستم SEO کامل تمامی نیازهای بهینه‌سازی موتورهای جستجو را پوشش می‌دهد و به شما کمک می‌کند تا رتبه بهتری در نتایج جستجو کسب کنید.

