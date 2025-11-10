#!/bin/bash

# اسکریپت آماده‌سازی کامل MTKoja برای انتقال به سرور آنلاین
# این اسکریپت تمام مراحل آماده‌سازی را به صورت خودکار انجام می‌دهد

echo "🚀 شروع آماده‌سازی کامل MTKoja برای انتقال به سرور آنلاین..."
echo "=========================================================="
echo ""

# بررسی وجود فایل‌های ضروری
if [ ! -f "composer.json" ]; then
    echo "❌ خطا: فایل composer.json یافت نشد. لطفاً در پوشه اصلی پروژه اجرا کنید."
    exit 1
fi

if [ ! -f "artisan" ]; then
    echo "❌ خطا: فایل artisan یافت نشد. لطفاً در پوشه backend اجرا کنید."
    exit 1
fi

echo "✅ بررسی فایل‌های ضروری انجام شد"
echo ""

# مرحله 1: بررسی و نصب وابستگی‌ها
echo "📦 مرحله 1: بررسی وابستگی‌ها..."
if ! command -v php &> /dev/null; then
    echo "❌ PHP نصب نشده است"
    exit 1
fi

if ! command -v composer &> /dev/null; then
    echo "❌ Composer نصب نشده است"
    exit 1
fi

if ! command -v mysql &> /dev/null; then
    echo "❌ MySQL نصب نشده است"
    exit 1
fi

echo "✅ تمام وابستگی‌ها موجود است"
echo ""

# مرحله 2: نصب وابستگی‌های Composer
echo "📦 مرحله 2: نصب وابستگی‌های Composer..."
composer install --no-dev --optimize-autoloader
if [ $? -ne 0 ]; then
    echo "❌ خطا در نصب وابستگی‌های Composer"
    exit 1
fi
echo "✅ وابستگی‌های Composer نصب شد"
echo ""

# مرحله 3: بررسی migration ها
echo "🔍 مرحله 3: بررسی migration ها..."
php verify-migrations.php
if [ $? -ne 0 ]; then
    echo "❌ خطا در بررسی migration ها"
    exit 1
fi
echo "✅ migration ها بررسی شد"
echo ""

# مرحله 4: آماده‌سازی دیتابیس
echo "💾 مرحله 4: آماده‌سازی دیتابیس..."
php prepare-production-database.php
if [ $? -ne 0 ]; then
    echo "❌ خطا در آماده‌سازی دیتابیس"
    exit 1
fi
echo "✅ دیتابیس آماده شد"
echo ""

# مرحله 5: ایجاد فایل‌های اضافی
echo "📄 مرحله 5: ایجاد فایل‌های اضافی..."

# ایجاد فایل .htaccess برای Apache
cat > production/.htaccess << 'EOF'
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
EOF

# ایجاد فایل robots.txt
cat > production/robots.txt << 'EOF'
User-agent: *
Allow: /

Sitemap: https://your-domain.com/sitemap.xml
EOF

# ایجاد فایل sitemap.xml نمونه
cat > production/sitemap.xml << 'EOF'
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
EOF

echo "✅ فایل‌های اضافی ایجاد شد"
echo ""

# مرحله 6: ایجاد اسکریپت تست
echo "🧪 مرحله 6: ایجاد اسکریپت تست..."
cat > production/test-deployment.sh << 'EOF'
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
EOF

chmod +x production/test-deployment.sh
echo "✅ اسکریپت تست ایجاد شد"
echo ""

# مرحله 7: ایجاد فایل README برای production
echo "📖 مرحله 7: ایجاد فایل README..."
cat > production/README.md << 'EOF'
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
EOF

echo "✅ فایل README ایجاد شد"
echo ""

# مرحله 8: نمایش خلاصه نهایی
echo "📊 خلاصه نهایی:"
echo "================"
echo ""

if [ -d "production" ]; then
    echo "📁 فایل‌های ایجاد شده در پوشه production:"
    ls -la production/
    echo ""
    
    echo "📏 اندازه فایل‌ها:"
    du -h production/*
    echo ""
fi

echo "✅ آماده‌سازی کامل شد!"
echo ""
echo "🎯 مراحل بعدی:"
echo "1. بررسی فایل‌های پوشه production"
echo "2. ویرایش فایل .env.production"
echo "3. انتقال فایل‌ها به سرور"
echo "4. اجرای اسکریپت نصب"
echo "5. تست و راه‌اندازی"
echo ""
echo "📖 برای جزئیات بیشتر، فایل DEPLOYMENT_INSTRUCTIONS.md را مطالعه کنید."
echo ""
echo "🎉 پروژه MTKoja آماده انتقال به سرور آنلاین است!"
