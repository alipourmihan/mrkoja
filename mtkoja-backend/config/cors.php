<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | این تنظیمات کنترل می‌کنند که چه مبداهایی می‌توانند به API شما دسترسی داشته
    | باشند، چه روش‌هایی مجاز هستند، و آیا اعتبارنامه‌ها (توکن‌ها یا کوکی‌ها)
    | پشتیبانی شوند یا نه.
    |
    */

    // مسیرهایی که CORS برایشان فعال است
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // متدهای HTTP مجاز
    'allowed_methods' => ['*'],

    // مبداهای مجاز
    'allowed_origins' => [
        'http://localhost:5173',   // توسعه لوکال (Vite)
        'http://localhost:3000',   // توسعه لوکال (Next/React)
        'http://127.0.0.1:5173',
        'http://127.0.0.1:3000',
        'https://mrkoja.com',      // سایت اصلی
        'https://www.mrkoja.com',
        'https://api.mrkoja.com',  // API اصلی
    ],

    // الگوهای مبدا مجاز (می‌توانید از regex استفاده کنید)
    'allowed_origins_patterns' => [
        '#^https://([a-z0-9-]+\.)?mrkoja\.com$#i',
    ],

    // هدرهای مجاز
    'allowed_headers' => ['*'],

    // هدرهایی که در پاسخ expose می‌شوند
    'exposed_headers' => [],

    // مدت زمان کش preflight در ثانیه
    'max_age' => 0,

    // آیا کوکی و توکن پشتیبانی شود
    'supports_credentials' => true,
];
