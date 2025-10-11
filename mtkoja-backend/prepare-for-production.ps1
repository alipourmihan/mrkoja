# اسکریپت آماده‌سازی کامل MTKoja برای انتقال به سرور آنلاین (PowerShell)
# این اسکریپت تمام مراحل آماده‌سازی را به صورت خودکار انجام می‌دهد

Write-Host "🚀 شروع آماده‌سازی کامل MTKoja برای انتقال به سرور آنلاین..." -ForegroundColor Green
Write-Host "==========================================================" -ForegroundColor Green
Write-Host ""

# بررسی وجود فایل‌های ضروری
if (-not (Test-Path "composer.json")) {
    Write-Host "❌ خطا: فایل composer.json یافت نشد. لطفاً در پوشه اصلی پروژه اجرا کنید." -ForegroundColor Red
    exit 1
}

if (-not (Test-Path "artisan")) {
    Write-Host "❌ خطا: فایل artisan یافت نشد. لطفاً در پوشه backend اجرا کنید." -ForegroundColor Red
    exit 1
}

Write-Host "✅ بررسی فایل‌های ضروری انجام شد" -ForegroundColor Green
Write-Host ""

# مرحله 1: بررسی و نصب وابستگی‌ها
Write-Host "📦 مرحله 1: بررسی وابستگی‌ها..." -ForegroundColor Yellow

try {
    $phpVersion = php --version
    Write-Host "✅ PHP موجود است" -ForegroundColor Green
} catch {
    Write-Host "❌ PHP نصب نشده است" -ForegroundColor Red
    exit 1
}

try {
    $composerVersion = composer --version
    Write-Host "✅ Composer موجود است" -ForegroundColor Green
} catch {
    Write-Host "❌ Composer نصب نشده است" -ForegroundColor Red
    exit 1
}

Write-Host "✅ تمام وابستگی‌ها موجود است" -ForegroundColor Green
Write-Host ""

# مرحله 2: نصب وابستگی‌های Composer
Write-Host "📦 مرحله 2: نصب وابستگی‌های Composer..." -ForegroundColor Yellow
try {
    composer install --no-dev --optimize-autoloader
    Write-Host "✅ وابستگی‌های Composer نصب شد" -ForegroundColor Green
} catch {
    Write-Host "❌ خطا در نصب وابستگی‌های Composer" -ForegroundColor Red
    exit 1
}
Write-Host ""

# مرحله 3: بررسی migration ها
Write-Host "🔍 مرحله 3: بررسی migration ها..." -ForegroundColor Yellow
try {
    php verify-migrations.php
    Write-Host "✅ migration ها بررسی شد" -ForegroundColor Green
} catch {
    Write-Host "❌ خطا در بررسی migration ها" -ForegroundColor Red
    exit 1
}
Write-Host ""

# مرحله 4: آماده‌سازی دیتابیس
Write-Host "💾 مرحله 4: آماده‌سازی دیتابیس..." -ForegroundColor Yellow
try {
    php prepare-production-database.php
    Write-Host "✅ دیتابیس آماده شد" -ForegroundColor Green
} catch {
    Write-Host "❌ خطا در آماده‌سازی دیتابیس" -ForegroundColor Red
    exit 1
}
Write-Host ""

# مرحله 5: ایجاد فایل‌های اضافی
Write-Host "📄 مرحله 5: ایجاد فایل‌های اضافی..." -ForegroundColor Yellow

# ایجاد فایل .htaccess برای Apache
$htaccessContent = @"
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
"@

$htaccessContent | Out-File -FilePath "production\.htaccess" -Encoding UTF8

# ایجاد فایل robots.txt
$robotsContent = @"
User-agent: *
Allow: /

Sitemap: https://your-domain.com/sitemap.xml
"@

$robotsContent | Out-File -FilePath "production\robots.txt" -Encoding UTF8

# ایجاد فایل sitemap.xml نمونه
$sitemapContent = @"
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://your-domain.com/</loc>
        <lastmod>2024-01-01</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>https://your-domain.com/about</loc>
        <lastmod>2024-01-01</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
</urlset>
"@

$sitemapContent | Out-File -FilePath "production\sitemap.xml" -Encoding UTF8

Write-Host "✅ فایل‌های اضافی ایجاد شد" -ForegroundColor Green
Write-Host ""

# مرحله 6: ایجاد اسکریپت تست
Write-Host "🧪 مرحله 6: ایجاد اسکریپت تست..." -ForegroundColor Yellow

$testScriptContent = @"
#!/bin/bash

echo "🧪 شروع تست deployment..."

# تست اتصال به دیتابیس
echo "🔍 تست اتصال به دیتابیس..."
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connection successful';"

# تست cache
echo "🔍 تست cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# تست API endpoints
echo "🔍 تست API endpoints..."
curl -s -o /dev/null -w "%{http_code}" http://localhost/api/test

echo "✅ تست deployment کامل شد"
"@

$testScriptContent | Out-File -FilePath "production\test-deployment.sh" -Encoding UTF8

Write-Host "✅ اسکریپت تست ایجاد شد" -ForegroundColor Green
Write-Host ""

# مرحله 7: ایجاد فایل README برای production
Write-Host "📖 مرحله 7: ایجاد فایل README..." -ForegroundColor Yellow

$readmeContent = @"
# MTKoja Production Files

این پوشه شامل تمام فایل‌های لازم برای انتقال پروژه MTKoja به سرور آنلاین است.

## فایل‌های موجود

- `database_backup_*.sql.gz` - Backup کامل دیتابیس
- `database_structure_*.sql` - ساختار دیتابیس (بدون داده)
- `initial_data_*.sql` - داده‌های اولیه
- `database_report_*.txt` - گزارش کامل ساختار دیتابیس
- `.env.production` - تنظیمات محیط production
- `install.sh` - اسکریپت نصب خودکار
- `test-deployment.sh` - اسکریپت تست deployment
- `DEPLOYMENT_INSTRUCTIONS.md` - دستورالعمل تفصیلی انتقال
- `.htaccess` - تنظیمات Apache
- `robots.txt` - فایل robots.txt
- `sitemap.xml` - نقشه سایت نمونه

## مراحل انتقال

1. آپلود تمام فایل‌ها به سرور
2. اجرای اسکریپت نصب: `./install.sh`
3. تست deployment: `./test-deployment.sh`
4. تنظیم وب سرور (Nginx/Apache)
5. فعال‌سازی SSL

## نکات مهم

- حتماً فایل `.env.production` را ویرایش کنید
- پسوردهای پیش‌فرض را تغییر دهید
- SSL را فعال کنید
- مانیتورینگ راه‌اندازی کنید

## پشتیبانی

در صورت بروز مشکل، فایل‌های لاگ و گزارش را بررسی کنید.
"@

$readmeContent | Out-File -FilePath "production\README.md" -Encoding UTF8

Write-Host "✅ فایل README ایجاد شد" -ForegroundColor Green
Write-Host ""

# مرحله 8: نمایش خلاصه نهایی
Write-Host "📊 خلاصه نهایی:" -ForegroundColor Cyan
Write-Host "================" -ForegroundColor Cyan
Write-Host ""

if (Test-Path "production") {
    Write-Host "📁 فایل‌های ایجاد شده در پوشه production:" -ForegroundColor Yellow
    Get-ChildItem production | Format-Table Name, Length, LastWriteTime
    Write-Host ""
    
    Write-Host "📏 اندازه فایل‌ها:" -ForegroundColor Yellow
    Get-ChildItem production | ForEach-Object {
        $size = [math]::Round($_.Length / 1KB, 2)
        Write-Host "$($_.Name): $size KB" -ForegroundColor White
    }
    Write-Host ""
}

Write-Host "✅ آماده‌سازی کامل شد!" -ForegroundColor Green
Write-Host ""
Write-Host "🎯 مراحل بعدی:" -ForegroundColor Cyan
Write-Host "1. بررسی فایل‌های پوشه production" -ForegroundColor White
Write-Host "2. ویرایش فایل .env.production" -ForegroundColor White
Write-Host "3. انتقال فایل‌ها به سرور" -ForegroundColor White
Write-Host "4. اجرای اسکریپت نصب" -ForegroundColor White
Write-Host "5. تست و راه‌اندازی" -ForegroundColor White
Write-Host ""
Write-Host "📖 برای جزئیات بیشتر، فایل DEPLOYMENT_INSTRUCTIONS.md را مطالعه کنید." -ForegroundColor Yellow
Write-Host ""
Write-Host "🎉 پروژه MTKoja آماده انتقال به سرور آنلاین است!" -ForegroundColor Green
