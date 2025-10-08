# راهنمای سریع شروع - سیستم SEO متکوجا

## 🚀 شروع سریع

### 1. اجرای سرور Laravel
```bash
cd mtkoja-backend
php artisan serve
```

### 2. دسترسی به سیستم
- **پنل ادمین SEO**: http://localhost:8000/admin/seo
- **تست ساده**: http://localhost:8000/simple-test.php
- **تست کامل**: http://localhost:8000/test-seo.html

### 3. API های اصلی
- **داشبورد**: http://localhost:8000/api/admin/seo/dashboard
- **لیست صفحات**: http://localhost:8000/api/admin/seo/pages
- **تولید Sitemap**: http://localhost:8000/api/seo/sitemap/generate
- **تولید robots.txt**: http://localhost:8000/api/seo/robots/generate

## 🔧 عیب‌یابی

### اگر چیزی نمایش نمی‌دهد:

1. **بررسی سرور**:
   ```bash
   php artisan serve
   ```

2. **اجرای Migration**:
   ```bash
   php artisan migrate
   ```

3. **پاک کردن کش**:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

4. **بررسی لاگ‌ها**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

## 📁 فایل‌های مهم

- `app/Http/Controllers/Admin/SeoAdminController.php` - کنترلر ادمین
- `app/Services/SeoService.php` - سرویس SEO
- `resources/views/admin/seo-dashboard.blade.php` - رابط کاربری
- `public/simple-test.php` - تست ساده
- `public/test-seo.html` - تست کامل

## 🎯 ویژگی‌های موجود

### ✅ پیاده‌سازی شده:
- داشبورد SEO با نمودارها
- مدیریت صفحات SEO
- تولید خودکار SEO
- اعتبارسنجی SEO
- تولید Sitemap و robots.txt
- گزارش‌ها و آمار
- رابط کاربری کامل

### 🔗 لینک‌های مفید:
- [مستندات کامل SEO](SEO_SYSTEM_README.md)
- [مستندات پنل ادمین](SEO_ADMIN_PANEL_README.md)
- [مثال‌های استفاده](SEO_USAGE_EXAMPLES.md)

## 🆘 پشتیبانی

اگر مشکلی دارید:
1. ابتدا فایل `simple-test.php` را باز کنید
2. وضعیت سیستم را بررسی کنید
3. لاگ‌های Laravel را چک کنید
4. از شناسه تغییرات `SEO_SYSTEM_v1.0` استفاده کنید

## 🎉 موفق باشید!

سیستم SEO متکوجا آماده استفاده است. از پنل ادمین برای مدیریت کامل SEO استفاده کنید.

