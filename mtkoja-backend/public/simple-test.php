<?php
// تست ساده سیستم SEO
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تست ساده SEO متکوجا</title>
    <style>
        body { font-family: Tahoma; direction: rtl; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin: 5px; }
        .result { background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 تست ساده سیستم SEO متکوجا</h1>
        
        <h2>📊 وضعیت سیستم</h2>
        <?php
        // بررسی وجود فایل‌ها
        $files = [
            'app/Http/Controllers/Admin/SeoAdminController.php' => 'کنترلر ادمین SEO',
            'app/Services/SeoService.php' => 'سرویس SEO',
            'resources/views/admin/seo-dashboard.blade.php' => 'صفحه ادمین',
            'database/migrations/2024_12_26_000000_enhance_seo_fields.php' => 'Migration SEO'
        ];
        
        foreach ($files as $file => $name) {
            if (file_exists($file)) {
                echo "<p class='success'>✅ $name: موجود</p>";
            } else {
                echo "<p class='error'>❌ $name: موجود نیست</p>";
            }
        }
        ?>
        
        <h2>🔗 لینک‌های دسترسی</h2>
        <p><a href="/admin/seo" target="_blank">🎛️ پنل ادمین SEO</a></p>
        <p><a href="/api/admin/seo/dashboard" target="_blank">📈 API داشبورد</a></p>
        <p><a href="/api/admin/seo/pages" target="_blank">📄 API لیست صفحات</a></p>
        <p><a href="/api/seo/sitemap/generate" target="_blank">🗺️ تولید Sitemap</a></p>
        <p><a href="/api/seo/robots/generate" target="_blank">🤖 تولید robots.txt</a></p>
        
        <h2>🧪 تست API</h2>
        <button onclick="testAPI()">تست API داشبورد</button>
        <div id="result" class="result" style="display: none;"></div>
        
        <h2>ℹ️ راهنمای استفاده</h2>
        <ol>
            <li>ابتدا مطمئن شوید سرور Laravel در حال اجرا است</li>
            <li>از لینک "پنل ادمین SEO" برای دسترسی به رابط کاربری استفاده کنید</li>
            <li>از API ها برای دسترسی برنامه‌نویسی استفاده کنید</li>
            <li>در صورت مشکل، لاگ‌های Laravel را بررسی کنید</li>
        </ol>
        
        <h2>🔧 دستورات مفید</h2>
        <pre>
# اجرای سرور
php artisan serve

# اجرای migration
php artisan migrate

# پاک کردن کش
php artisan cache:clear

# مشاهده لاگ‌ها
tail -f storage/logs/laravel.log
        </pre>
    </div>
    
    <script>
        async function testAPI() {
            const resultDiv = document.getElementById('result');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '🔄 در حال تست...';
            
            try {
                const response = await fetch('/api/admin/seo/dashboard');
                const data = await response.json();
                
                if (response.ok) {
                    resultDiv.innerHTML = '<div class="success">✅ API کار می‌کند!</div><pre>' + JSON.stringify(data, null, 2) + '</pre>';
                } else {
                    resultDiv.innerHTML = '<div class="error">❌ خطا در API: ' + response.status + '</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="error">❌ خطا: ' + error.message + '</div>';
            }
        }
    </script>
</body>
</html>

