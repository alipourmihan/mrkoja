<?php

/**
 * Database Backup Script
 * این اسکریپت برای backup گرفتن از دیتابیس استفاده می‌شود
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

// تنظیمات محیط
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "💾 شروع backup از دیتابیس...\n\n";

try {
    // دریافت تنظیمات دیتابیس
    $config = Config::get('database.connections.mysql');
    
    $host = $config['host'];
    $port = $config['port'];
    $database = $config['database'];
    $username = $config['username'];
    $password = $config['password'];
    
    // ایجاد نام فایل backup
    $backupFile = 'backups/backup_' . date('Y-m-d_H-i-s') . '.sql';
    
    // ایجاد پوشه backups اگر وجود ندارد
    if (!is_dir('backups')) {
        mkdir('backups', 0755, true);
    }
    
    // دستور mysqldump
    $command = sprintf(
        'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
        escapeshellarg($host),
        escapeshellarg($port),
        escapeshellarg($username),
        escapeshellarg($password),
        escapeshellarg($database),
        escapeshellarg($backupFile)
    );
    
    echo "📡 در حال backup گرفتن...\n";
    exec($command, $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ Backup با موفقیت ایجاد شد: $backupFile\n";
        
        // فشرده‌سازی فایل
        $compressedFile = $backupFile . '.gz';
        exec("gzip $backupFile");
        echo "🗜️ فایل فشرده شد: $compressedFile\n";
        
        // نمایش اندازه فایل
        $fileSize = filesize($compressedFile);
        $fileSizeFormatted = $fileSize > 1024 * 1024 
            ? round($fileSize / (1024 * 1024), 2) . ' MB'
            : round($fileSize / 1024, 2) . ' KB';
        
        echo "📊 اندازه فایل: $fileSizeFormatted\n";
        
    } else {
        echo "❌ خطا در ایجاد backup\n";
        echo "خروجی: " . implode("\n", $output) . "\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "❌ خطا در backup:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 Backup کامل شد!\n";
