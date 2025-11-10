# راهنمای انتقال پروژه به سرور Production

## مراحل انتقال به سرور

### 1. آماده‌سازی فایل‌ها

```bash
# کپی کردن فایل‌ها به سرور
rsync -avz --exclude 'node_modules' --exclude '.git' --exclude 'storage/logs' ./ user@server:/path/to/project/
```

### 2. تنظیمات سرور

#### نصب Dependencies
```bash
# در سرور
cd /path/to/project/mtkoja-backend
composer install --no-dev --optimize-autoloader

cd ../mtkoja-frontend
npm install
npm run build
```

#### تنظیم فایل .env
```bash
# کپی کردن فایل .env.example
cp .env.example .env

# ویرایش تنظیمات
nano .env
```

تنظیمات مهم در `.env`:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. اجرای Migration ها

#### روش 1: استفاده از اسکریپت خودکار
```bash
php deploy-migrations.php
```

#### روش 2: اجرای دستی
```bash
# پاک کردن cache ها
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# اجرای migration ها
php artisan migrate --force

# اجرای seeders (اختیاری)
php artisan db:seed --force

# بهینه‌سازی
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. تنظیمات وب سرور

#### Apache (.htaccess)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/project/mtkoja-backend/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 5. تنظیمات مجوزها

```bash
# تنظیم مجوزهای صحیح
sudo chown -R www-data:www-data /path/to/project
sudo chmod -R 755 /path/to/project
sudo chmod -R 775 /path/to/project/storage
sudo chmod -R 775 /path/to/project/bootstrap/cache
```

### 6. تست عملکرد

```bash
# تست API
curl -X GET https://yourdomain.com/api/test

# بررسی لاگ‌ها
tail -f storage/logs/laravel.log
```

## مشکلات رایج و راه‌حل‌ها

### 1. خطای Migration
```bash
# اگر migration ها fail شدند
php artisan migrate:status
php artisan migrate:rollback --step=1
php artisan migrate
```

### 2. خطای Permission
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 3. خطای Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 4. خطای Database Connection
- بررسی تنظیمات دیتابیس در `.env`
- اطمینان از وجود دیتابیس
- بررسی مجوزهای کاربر دیتابیس

## بهینه‌سازی Performance

### 1. PHP-FPM
```ini
; /etc/php/8.4/fpm/pool.d/www.conf
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
```

### 2. MySQL
```sql
-- بهینه‌سازی MySQL
SET GLOBAL innodb_buffer_pool_size = 1G;
SET GLOBAL query_cache_size = 64M;
```

### 3. Redis (اختیاری)
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

## Monitoring و Logging

### 1. لاگ‌ها
```bash
# مشاهده لاگ‌ها
tail -f storage/logs/laravel.log

# لاگ‌های خطا
grep "ERROR" storage/logs/laravel.log
```

### 2. Performance Monitoring
```bash
# نصب Laravel Telescope (اختیاری)
composer require laravel/telescope --dev
php artisan telescope:install
```

## Backup و Recovery

### 1. Backup دیتابیس
```bash
mysqldump -u username -p database_name > backup.sql
```

### 2. Backup فایل‌ها
```bash
tar -czf project_backup.tar.gz /path/to/project
```

## امنیت

### 1. تنظیمات امنیتی
- تغییر `APP_KEY` در production
- تنظیم `APP_DEBUG=false`
- استفاده از HTTPS
- تنظیم مجوزهای صحیح فایل‌ها

### 2. Firewall
```bash
# تنظیم UFW
sudo ufw allow 22
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable
```

---

## نکات مهم

1. **همیشه قبل از migration در production، backup بگیرید**
2. **ابتدا در محیط staging تست کنید**
3. **از version control استفاده کنید**
4. **لاگ‌ها را مرتب بررسی کنید**
5. **Performance را monitor کنید**

برای سوالات بیشتر، با تیم توسعه تماس بگیرید.
