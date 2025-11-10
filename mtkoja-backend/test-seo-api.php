<?php
/**
 * تست API های SEO
 */

// تنظیمات
$baseUrl = 'http://localhost:8000/api';
$testResults = [];

// تابع تست API
function testApi($url, $method = 'GET', $data = null) {
    global $testResults;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    $result = [
        'url' => $url,
        'method' => $method,
        'http_code' => $httpCode,
        'success' => $httpCode >= 200 && $httpCode < 300,
        'error' => $error,
        'response' => $response ? json_decode($response, true) : null
    ];
    
    $testResults[] = $result;
    return $result;
}

echo "<h1>تست API های SEO متکوجا</h1>\n";
echo "<style>body{font-family:Arial;direction:rtl;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>\n";

// تست 1: داشبورد SEO
echo "<h2>1. تست داشبورد SEO</h2>\n";
$result = testApi($baseUrl . '/admin/seo/dashboard');
if ($result['success']) {
    echo "<p class='success'>✅ داشبورد SEO: موفق</p>\n";
    echo "<pre>" . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>\n";
} else {
    echo "<p class='error'>❌ داشبورد SEO: خطا - " . $result['http_code'] . "</p>\n";
    if ($result['error']) {
        echo "<p class='error'>خطا: " . $result['error'] . "</p>\n";
    }
}

// تست 2: لیست صفحات SEO
echo "<h2>2. تست لیست صفحات SEO</h2>\n";
$result = testApi($baseUrl . '/admin/seo/pages');
if ($result['success']) {
    echo "<p class='success'>✅ لیست صفحات SEO: موفق</p>\n";
    echo "<pre>" . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>\n";
} else {
    echo "<p class='error'>❌ لیست صفحات SEO: خطا - " . $result['http_code'] . "</p>\n";
}

// تست 3: تولید Sitemap
echo "<h2>3. تست تولید Sitemap</h2>\n";
$result = testApi($baseUrl . '/seo/sitemap/generate', 'GET');
if ($result['success']) {
    echo "<p class='success'>✅ تولید Sitemap: موفق</p>\n";
    echo "<pre>" . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>\n";
} else {
    echo "<p class='error'>❌ تولید Sitemap: خطا - " . $result['http_code'] . "</p>\n";
}

// تست 4: تولید robots.txt
echo "<h2>4. تست تولید robots.txt</h2>\n";
$result = testApi($baseUrl . '/seo/robots/generate', 'GET');
if ($result['success']) {
    echo "<p class='success'>✅ تولید robots.txt: موفق</p>\n";
    echo "<pre>" . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>\n";
} else {
    echo "<p class='error'>❌ تولید robots.txt: خطا - " . $result['http_code'] . "</p>\n";
}

// تست 5: آمار SEO
echo "<h2>5. تست آمار SEO</h2>\n";
$result = testApi($baseUrl . '/seo/stats', 'GET');
if ($result['success']) {
    echo "<p class='success'>✅ آمار SEO: موفق</p>\n";
    echo "<pre>" . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>\n";
} else {
    echo "<p class='error'>❌ آمار SEO: خطا - " . $result['http_code'] . "</p>\n";
}

// تست 6: صفحه اصلی SEO
echo "<h2>6. تست صفحه اصلی SEO</h2>\n";
$result = testApi($baseUrl . '/seo/homepage/schema', 'GET');
if ($result['success']) {
    echo "<p class='success'>✅ صفحه اصلی SEO: موفق</p>\n";
    echo "<pre>" . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>\n";
} else {
    echo "<p class='error'>❌ صفحه اصلی SEO: خطا - " . $result['http_code'] . "</p>\n";
}

// خلاصه نتایج
echo "<h2>خلاصه نتایج</h2>\n";
$successCount = 0;
$totalCount = count($testResults);

foreach ($testResults as $test) {
    if ($test['success']) {
        $successCount++;
    }
}

echo "<p class='info'>تعداد تست‌های موفق: $successCount از $totalCount</p>\n";

if ($successCount === $totalCount) {
    echo "<p class='success'>🎉 همه تست‌ها موفق بودند!</p>\n";
} else {
    echo "<p class='error'>⚠️ برخی تست‌ها ناموفق بودند</p>\n";
}

echo "<h2>دسترسی به پنل ادمین</h2>\n";
echo "<p><a href='http://localhost:8000/admin/seo' target='_blank'>🔗 پنل ادمین SEO</a></p>\n";

echo "<h2>دسترسی به API ها</h2>\n";
echo "<ul>\n";
echo "<li><a href='$baseUrl/admin/seo/dashboard' target='_blank'>داشبورد SEO</a></li>\n";
echo "<li><a href='$baseUrl/admin/seo/pages' target='_blank'>لیست صفحات</a></li>\n";
echo "<li><a href='$baseUrl/seo/sitemap/generate' target='_blank'>تولید Sitemap</a></li>\n";
echo "<li><a href='$baseUrl/seo/robots/generate' target='_blank'>تولید robots.txt</a></li>\n";
echo "<li><a href='$baseUrl/seo/stats' target='_blank'>آمار SEO</a></li>\n";
echo "</ul>\n";
?>

