# راهنمای نصب mtkoja

## پیش‌نیازها

- PHP 8.4 یا بالاتر
- Composer
- Node.js 18 یا بالاتر
- npm یا yarn
- MySQL یا SQLite

## روش 1: نصب دستی

### 1. کلون کردن پروژه
```bash
git clone <repository-url>
cd mtkoja
```

### 2. راه‌اندازی Backend (Laravel)

```bash
cd mtkoja-backend

# نصب dependencies
composer install

# کپی کردن فایل محیط
cp .env.example .env

# تولید کلید اپلیکیشن
php artisan key:generate

# اجرای migrations
php artisan migrate

# اجرای seeders
php artisan db:seed

# راه‌اندازی سرور
php artisan serve
```

### 3. راه‌اندازی Frontend (Vue.js)

```bash
cd mtkoja-frontend

# نصب dependencies
npm install

# راه‌اندازی سرور توسعه
npm run dev
```

## روش 2: استفاده از Docker

```bash
# راه‌اندازی تمام سرویس‌ها
docker-compose up -d

# اجرای migrations
docker-compose exec backend php artisan migrate

# اجرای seeders
docker-compose exec backend php artisan db:seed
```

## دسترسی‌ها

- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000/api
- **Laravel Admin**: http://localhost:8000

## حساب‌های پیش‌فرض

پس از اجرای seeders، حساب‌های زیر ایجاد می‌شوند:

- **مدیر**: admin@mtkoja.com
- **صاحب کسب‌وکار**: owner@mtkoja.com

رمز عبور برای هر دو حساب: `password`

## تنظیمات پایگاه داده

### SQLite (پیش‌فرض)
پایگاه داده SQLite به صورت خودکار در `mtkoja-backend/database/database.sqlite` ایجاد می‌شود.

### MySQL
برای استفاده از MySQL، فایل `.env` را ویرایش کنید:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mtkoja
DB_USERNAME=root
DB_PASSWORD=your_password
```

## عیب‌یابی

### مشکل CORS
اگر با مشکل CORS مواجه شدید، فایل `mtkoja-backend/config/cors.php` را بررسی کنید.

### مشکل اتصال به API
اطمینان حاصل کنید که:
- سرور Laravel روی پورت 8000 اجرا می‌شود
- URL API در فایل `mtkoja-frontend/src/stores/auth.js` صحیح است

### مشکل Tailwind CSS
اگر استایل‌ها اعمال نمی‌شوند:
```bash
cd mtkoja-frontend
npm run build
```

## ساخت برای تولید

### Backend
```bash
cd mtkoja-backend
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Frontend
```bash
cd mtkoja-frontend
npm run build
```

## پشتیبانی

برای گزارش مشکل یا درخواست ویژگی جدید، لطفاً issue ایجاد کنید.









