<?php

/**
 * Migration Verification Script
 * اسکریپت بررسی و آماده‌سازی migration ها برای production
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

// تنظیمات محیط
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 بررسی migration ها برای production...\n\n";

try {
    // بررسی migration های اجرا شده
    $migrations = DB::table('migrations')->orderBy('batch')->orderBy('migration')->get();
    
    echo "📋 لیست migration های اجرا شده:\n";
    echo "================================\n";
    
    $batch = 0;
    foreach ($migrations as $migration) {
        if ($migration->batch != $batch) {
            $batch = $migration->batch;
            echo "\n🔸 Batch $batch:\n";
        }
        echo "  ✅ {$migration->migration}\n";
    }
    
    // بررسی جداول موجود
    echo "\n📊 بررسی جداول موجود:\n";
    echo "=====================\n";
    
    $tables = DB::select("SHOW TABLES");
    $expectedTables = [
        'users',
        'provinces', 
        'cities',
        'neighborhoods',
        'categories',
        'businesses',
        'reviews',
        'images',
        'features',
        'business_features',
        'views',
        'seo_pages',
        'personal_access_tokens',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        'migrations',
        'password_reset_tokens',
        'sessions'
    ];
    
    $existingTables = [];
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        $existingTables[] = $tableName;
        echo "  ✅ $tableName\n";
    }
    
    // بررسی جداول مفقود
    $missingTables = array_diff($expectedTables, $existingTables);
    if (!empty($missingTables)) {
        echo "\n⚠️ جداول مفقود:\n";
        foreach ($missingTables as $table) {
            echo "  ❌ $table\n";
        }
    }
    
    // بررسی ساختار جداول مهم
    echo "\n🔍 بررسی ساختار جداول مهم:\n";
    echo "==========================\n";
    
    $importantTables = ['users', 'businesses', 'categories', 'provinces', 'cities'];
    
    foreach ($importantTables as $table) {
        if (in_array($table, $existingTables)) {
            echo "\n📋 ساختار جدول $table:\n";
            $columns = DB::select("DESCRIBE `$table`");
            
            foreach ($columns as $column) {
                $nullable = $column->Null === 'YES' ? 'NULL' : 'NOT NULL';
                $key = $column->Key ? " ({$column->Key})" : '';
                echo "  • {$column->Field}: {$column->Type} $nullable$key\n";
            }
            
            // بررسی ایندکس‌ها
            $indexes = DB::select("SHOW INDEX FROM `$table`");
            $uniqueIndexes = array_filter($indexes, function($index) {
                return $index->Non_unique == 0 && $index->Key_name != 'PRIMARY';
            });
            
            if (!empty($uniqueIndexes)) {
                echo "  🔑 ایندکس‌های منحصر به فرد:\n";
                foreach ($uniqueIndexes as $index) {
                    echo "    • {$index->Key_name} ({$index->Column_name})\n";
                }
            }
        }
    }
    
    // بررسی داده‌های اولیه
    echo "\n🌱 بررسی داده‌های اولیه:\n";
    echo "======================\n";
    
    $seedData = [
        'provinces' => 'استان‌ها',
        'cities' => 'شهرها', 
        'neighborhoods' => 'محله‌ها',
        'categories' => 'دسته‌بندی‌ها',
        'features' => 'ویژگی‌ها',
        'users' => 'کاربران'
    ];
    
    foreach ($seedData as $table => $description) {
        if (in_array($table, $existingTables)) {
            $count = DB::table($table)->count();
            echo "  • $description ($table): $count رکورد\n";
        }
    }
    
    // بررسی مشکلات احتمالی
    echo "\n⚠️ بررسی مشکلات احتمالی:\n";
    echo "========================\n";
    
    $issues = [];
    
    // بررسی کاربران بدون نقش
    if (in_array('users', $existingTables)) {
        $usersWithoutRole = DB::table('users')->whereNull('role')->count();
        if ($usersWithoutRole > 0) {
            $issues[] = "کاربران بدون نقش: $usersWithoutRole کاربر";
        }
    }
    
    // بررسی business های بدون دسته‌بندی
    if (in_array('businesses', $existingTables)) {
        $businessesWithoutCategory = DB::table('businesses')->whereNull('category_id')->count();
        if ($businessesWithoutCategory > 0) {
            $issues[] = "کسب‌وکارهای بدون دسته‌بندی: $businessesWithoutCategory مورد";
        }
    }
    
    // بررسی تصاویر بدون business
    if (in_array('images', $existingTables)) {
        $orphanImages = DB::table('images')->whereNull('business_id')->count();
        if ($orphanImages > 0) {
            $issues[] = "تصاویر بدون کسب‌وکار: $orphanImages تصویر";
        }
    }
    
    if (empty($issues)) {
        echo "  ✅ هیچ مشکل جدی یافت نشد\n";
    } else {
        foreach ($issues as $issue) {
            echo "  ⚠️ $issue\n";
        }
    }
    
    // تولید گزارش نهایی
    echo "\n📄 تولید گزارش نهایی...\n";
    $reportFile = "production/migration_report_" . date('Y-m-d_H-i-s') . ".txt";
    
    if (!is_dir('production')) {
        mkdir('production', 0755, true);
    }
    
    $report = generateMigrationReport($migrations, $existingTables, $issues);
    file_put_contents($reportFile, $report);
    echo "✅ گزارش migration در فایل $reportFile ذخیره شد\n";
    
    echo "\n🎉 بررسی migration ها کامل شد!\n";
    
    if (empty($issues)) {
        echo "✅ دیتابیس آماده انتقال به production است.\n";
    } else {
        echo "⚠️ لطفاً مشکلات بالا را قبل از انتقال برطرف کنید.\n";
    }
    
} catch (Exception $e) {
    echo "❌ خطا در بررسی migration ها:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}

/**
 * تولید گزارش کامل migration ها
 */
function generateMigrationReport($migrations, $existingTables, $issues) {
    $report = "گزارش Migration ها - " . date('Y-m-d H:i:s') . "\n";
    $report .= "========================================\n\n";
    
    $report .= "📋 Migration های اجرا شده:\n";
    $report .= "==========================\n";
    
    $batch = 0;
    foreach ($migrations as $migration) {
        if ($migration->batch != $batch) {
            $batch = $migration->batch;
            $report .= "\nBatch $batch:\n";
        }
        $report .= "✅ {$migration->migration}\n";
    }
    
    $report .= "\n📊 جداول موجود:\n";
    $report .= "===============\n";
    
    foreach ($existingTables as $table) {
        $report .= "✅ $table\n";
    }
    
    if (!empty($issues)) {
        $report .= "\n⚠️ مشکلات یافت شده:\n";
        $report .= "===================\n";
        
        foreach ($issues as $issue) {
            $report .= "⚠️ $issue\n";
        }
    } else {
        $report .= "\n✅ هیچ مشکل جدی یافت نشد\n";
    }
    
    $report .= "\n📈 آمار کلی:\n";
    $report .= "============\n";
    $report .= "تعداد migration ها: " . count($migrations) . "\n";
    $report .= "تعداد جداول: " . count($existingTables) . "\n";
    $report .= "تعداد مشکلات: " . count($issues) . "\n";
    
    return $report;
}
