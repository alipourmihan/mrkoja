<?php

/**
 * Production Migration Script
 * این اسکریپت برای اجرای migration ها در سرور production استفاده می‌شود
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

// تنظیمات محیط production
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🚀 شروع migration ها برای production...\n\n";

try {
    // 1. بررسی اتصال دیتابیس
    echo "📡 بررسی اتصال دیتابیس...\n";
    DB::connection()->getPdo();
    echo "✅ اتصال دیتابیس برقرار است\n\n";

    // 2. پاک کردن cache ها
    echo "🧹 پاک کردن cache ها...\n";
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    echo "✅ Cache ها پاک شدند\n\n";

    // 3. اجرای migration ها
    echo "📦 اجرای migration ها...\n";
    Artisan::call('migrate', ['--force' => true]);
    echo "✅ Migration ها با موفقیت اجرا شدند\n\n";

    // 4. اجرای seeders (اختیاری)
    echo "🌱 اجرای seeders...\n";
    Artisan::call('db:seed', ['--force' => true]);
    echo "✅ Seeders با موفقیت اجرا شدند\n\n";

    // 5. بهینه‌سازی
    echo "⚡ بهینه‌سازی...\n";
    Artisan::call('config:cache');
    Artisan::call('route:cache');
    Artisan::call('view:cache');
    echo "✅ بهینه‌سازی کامل شد\n\n";

    echo "🎉 تمام عملیات با موفقیت انجام شد!\n";

} catch (Exception $e) {
    echo "❌ خطا در اجرای migration ها:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}
