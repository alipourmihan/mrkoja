# mtkoja - پلتفرم معرفی کسب‌وکارها

پلتفرم کامل معرفی بهترین کسب‌وکارها و حوزه‌های فعالیت با استفاده از Laravel و Vue.js

## ویژگی‌ها

- 🏠 **صفحه اصلی**: نمایش دسته‌بندی‌ها و کسب‌وکارهای برتر
- 👤 **سیستم احراز هویت**: ثبت‌نام و ورود با نقش‌های مختلف
- 🏢 **پنل کسب‌وکار**: مدیریت کسب‌وکارهای شخصی
- ⚙️ **پنل مدیریت**: مدیریت کامل سیستم
- 📱 **طراحی ریسپانسیو**: سازگار با تمام دستگاه‌ها

## تکنولوژی‌ها

### Backend
- Laravel 12
- Laravel Sanctum (API Authentication)
- MySQL/SQLite
- RESTful API

### Frontend
- Vue.js 3
- Vue Router 4
- Pinia (State Management)
- Tailwind CSS
- Axios

## نصب و راه‌اندازی

### Backend (Laravel)

```bash
cd mtkoja-backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

### Frontend (Vue.js)

```bash
cd mtkoja-frontend
npm install
npm run dev
```

## دسترسی‌ها

- **صفحه اصلی**: http://localhost:5173
- **API Backend**: http://localhost:8000/api
- **پنل مدیریت**: http://localhost:5173/admin
- **پنل کسب‌وکار**: http://localhost:5173/business

## حساب‌های پیش‌فرض

- **مدیر**: admin@mtkoja.com
- **صاحب کسب‌وکار**: owner@mtkoja.com

## API Endpoints

### عمومی
- `GET /api/categories` - لیست دسته‌بندی‌ها
- `GET /api/businesses` - لیست کسب‌وکارها
- `POST /api/register` - ثبت‌نام
- `POST /api/login` - ورود

### محافظت شده
- `GET /api/user` - اطلاعات کاربر
- `POST /api/logout` - خروج
- `POST /api/businesses` - ایجاد کسب‌وکار (صاحب کسب‌وکار)
- `GET /api/my-businesses` - کسب‌وکارهای من
- `GET /api/admin/stats` - آمار کلی (مدیر)

## ساختار پروژه

```
mtkoja-backend/
├── app/
│   ├── Http/Controllers/Api/
│   ├── Models/
│   └── Http/Middleware/
├── database/migrations/
└── routes/api.php

mtkoja-frontend/
├── src/
│   ├── components/
│   ├── pages/
│   ├── stores/
│   └── main.js
└── tailwind.config.js
```

## مشارکت

برای مشارکت در پروژه، لطفاً مراحل زیر را دنبال کنید:

1. Fork کنید
2. Branch جدید ایجاد کنید
3. تغییرات را commit کنید
4. Pull Request ارسال کنید

## لایسنس

MIT License