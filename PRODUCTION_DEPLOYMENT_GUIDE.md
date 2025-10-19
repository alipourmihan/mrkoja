# راهنمای کامل انتقال MTKoja به سرور آنلاین

## 📋 فهرست مطالب
1. [آماده‌سازی اولیه](#آماده‌سازی-اولیه)
2. [آماده‌سازی دیتابیس](#آماده‌سازی-دیتابیس)
3. [انتقال فایل‌ها](#انتقال-فایل‌ها)
4. [تنظیم سرور](#تنظیم-سرور)
5. [نصب و راه‌اندازی](#نصب-و-راه‌اندازی)
6. [تست و بررسی](#تست-و-بررسی)
7. [تنظیمات امنیتی](#تنظیمات-امنیتی)
8. [مانیتورینگ](#مانیتورینگ)

---

## 🚀 آماده‌سازی اولیه

### پیش‌نیازها
- سرور با PHP 8.4+ 
- MySQL 8.0+ یا MariaDB 10.3+
- Composer
- Git (اختیاری)
- دسترسی SSH به سرور

### بررسی سیستم محلی
```bash
# بررسی نسخه PHP
php --version

# بررسی Composer
composer --version

# بررسی MySQL
mysql --version
```

---

## 💾 آماده‌سازی دیتابیس

### مرحله 1: اجرای اسکریپت آماده‌سازی
```bash
cd mrkoja/mtkoja-backend
php prepare-production-database.php
```

این اسکریپت فایل‌های زیر را ایجاد می‌کند:
- `production/database_backup_YYYY-MM-DD_HH-MM-SS.sql.gz` - Backup کامل
- `production/database_structure_YYYY-MM-DD_HH-MM-SS.sql` - ساختار دیتابیس
- `production/initial_data_YYYY-MM-DD_HH-MM-SS.sql` - داده‌های اولیه
- `production/database_report_YYYY-MM-DD_HH-MM-SS.txt` - گزارش کامل
- `production/.env.production` - تنظیمات محیط
- `production/install.sh` - اسکریپت نصب خودکار
- `production/DEPLOYMENT_INSTRUCTIONS.md` - دستورالعمل تفصیلی

### مرحله 2: بررسی migration ها
```bash
php verify-migrations.php
```

### مرحله 3: تست backup
```bash
# تست بازیابی backup در محیط محلی
gunzip production/database_backup_*.sql.gz
mysql -u root -p test_database < production/database_backup_*.sql
```

---

## 📁 انتقال فایل‌ها

### روش 1: استفاده از Git (توصیه شده)
```bash
# در سرور
git clone https://github.com/your-repo/mtkoja.git
cd mtkoja
```

### روش 2: انتقال مستقیم
```bash
# فشرده‌سازی پروژه
tar -czf mtkoja.tar.gz mrkoja/

# انتقال به سرور
scp mtkoja.tar.gz user@server:/path/to/destination/

# در سرور
tar -xzf mtkoja.tar.gz
```

### روش 3: استفاده از rsync
```bash
rsync -avz --exclude 'node_modules' --exclude '.git' \
  mrkoja/ user@server:/path/to/destination/mrkoja/
```

---

## ⚙️ تنظیم سرور

### نصب وابستگی‌ها

#### Ubuntu/Debian:
```bash
sudo apt update
sudo apt install php8.4 php8.4-cli php8.4-fpm php8.4-mysql php8.4-xml php8.4-mbstring php8.4-curl php8.4-zip php8.4-gd
sudo apt install mysql-server nginx composer
```

#### CentOS/RHEL:
```bash
sudo yum install php81 php81-php-cli php81-php-fpm php81-php-mysql php81-php-xml php81-php-mbstring php81-php-curl php81-php-zip php81-php-gd
sudo yum install mysql-server nginx composer
```

### تنظیم PHP
```bash
# ویرایش فایل php.ini
sudo nano /etc/php/8.4/fpm/php.ini

# تنظیمات مهم:
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
memory_limit = 256M
```

### تنظیم MySQL
```bash
# ورود به MySQL
sudo mysql -u root -p

# ایجاد دیتابیس و کاربر
CREATE DATABASE mtkoja_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mtkoja_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON mtkoja_production.* TO 'mtkoja_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 🔧 نصب و راه‌اندازی

### مرحله 1: تنظیم پروژه
```bash
cd /path/to/mtkoja/mtkoja-backend

# نصب وابستگی‌ها
composer install --no-dev --optimize-autoloader

# تنظیم مجوزها
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### مرحله 2: تنظیم محیط
```bash
# کپی فایل تنظیمات
cp production/.env.production .env

# ویرایش تنظیمات
nano .env
```

تنظیمات مهم در `.env`:
```env
APP_NAME="MTKoja"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mtkoja_production
DB_USERNAME=mtkoja_user
DB_PASSWORD=strong_password_here

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### مرحله 3: تولید کلید اپلیکیشن
```bash
php artisan key:generate
```

### مرحله 4: بازیابی دیتابیس
```bash
# گزینه 1: استفاده از backup کامل
gunzip production/database_backup_*.sql.gz
mysql -u mtkoja_user -p mtkoja_production < production/database_backup_*.sql

# گزینه 2: استفاده از migration + seeder
php artisan migrate --force
php artisan db:seed --force
```

### مرحله 5: بهینه‌سازی
```bash
# پاک کردن cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# تنظیم مجوزهای نهایی
chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 storage bootstrap/cache
```

---

## 🌐 تنظیم وب سرور

### تنظیم Nginx
```bash
sudo nano /etc/nginx/sites-available/mtkoja
```

محتوای فایل:
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /path/to/mtkoja/mtkoja-backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

فعال‌سازی سایت:
```bash
sudo ln -s /etc/nginx/sites-available/mtkoja /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### تنظیم SSL (Let's Encrypt)
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

---

## ✅ تست و بررسی

### تست‌های اولیه
```bash
# تست دسترسی به سایت
curl -I https://your-domain.com

# تست API
curl -X GET https://your-domain.com/api/test

# بررسی لاگ‌ها
tail -f storage/logs/laravel.log
```

### تست‌های عملکردی
1. **تست ورود کاربران**
2. **تست ثبت کسب‌وکار**
3. **تست آپلود تصاویر**
4. **تست جستجو**
5. **تست SEO**

### تست‌های امنیتی
```bash
# بررسی فایل‌های حساس
ls -la .env
ls -la storage/

# بررسی مجوزها
find . -type f -perm 777
```

---

## 🔒 تنظیمات امنیتی

### تنظیمات فایل‌ها
```bash
# تنظیم مجوزهای صحیح
chmod 644 .env
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# حذف فایل‌های غیرضروری
rm -rf tests/
rm -rf .git/
```

### تنظیمات دیتابیس
```sql
-- تغییر پسورد root
ALTER USER 'root'@'localhost' IDENTIFIED BY 'new_strong_password';

-- حذف کاربران غیرضروری
DROP USER IF EXISTS ''@'localhost';
DROP USER IF EXISTS ''@'%';
```

### تنظیمات سرور
```bash
# تنظیم فایروال
sudo ufw enable
sudo ufw allow 22
sudo ufw allow 80
sudo ufw allow 443

# تنظیم fail2ban
sudo apt install fail2ban
sudo systemctl enable fail2ban
```

---

## 📊 مانیتورینگ

### تنظیم Cron Jobs
```bash
# ویرایش crontab
crontab -e

# اضافه کردن job های Laravel
* * * * * cd /path/to/mtkoja/mtkoja-backend && php artisan schedule:run >> /dev/null 2>&1
```

### تنظیم Log Rotation
```bash
sudo nano /etc/logrotate.d/mtkoja
```

محتوای فایل:
```
/path/to/mtkoja/mtkoja-backend/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 644 www-data www-data
}
```

### مانیتورینگ سیستم
```bash
# نصب htop برای مانیتورینگ
sudo apt install htop

# مانیتورینگ فضای دیسک
df -h

# مانیتورینگ حافظه
free -h
```

---

## 🚨 عیب‌یابی

### مشکلات رایج

#### خطای 500 Internal Server Error
```bash
# بررسی لاگ‌ها
tail -f storage/logs/laravel.log
tail -f /var/log/nginx/error.log

# بررسی مجوزها
ls -la storage/
ls -la bootstrap/cache/
```

#### مشکل اتصال به دیتابیس
```bash
# تست اتصال
mysql -u mtkoja_user -p mtkoja_production

# بررسی تنظیمات
php artisan config:show database
```

#### مشکل آپلود فایل
```bash
# بررسی تنظیمات PHP
php -i | grep upload_max_filesize
php -i | grep post_max_size

# بررسی مجوزهای پوشه storage
ls -la storage/app/public/
```

---

## 📞 پشتیبانی

### اطلاعات مفید برای پشتیبانی
- نسخه PHP: `php --version`
- نسخه MySQL: `mysql --version`
- نسخه Composer: `composer --version`
- لاگ‌های خطا: `storage/logs/laravel.log`
- گزارش migration: `production/migration_report_*.txt`

### تماس با تیم پشتیبانی
در صورت بروز مشکل، اطلاعات زیر را ارسال کنید:
1. پیام خطای کامل
2. فایل لاگ مربوطه
3. تنظیمات سرور
4. مراحل انجام شده

---

## 📝 چک‌لیست نهایی

- [ ] سرور آماده و تنظیم شده
- [ ] دیتابیس ایجاد و بازیابی شده
- [ ] فایل‌های پروژه انتقال یافته
- [ ] تنظیمات محیط انجام شده
- [ ] وب سرور تنظیم شده
- [ ] SSL فعال شده
- [ ] تست‌های اولیه انجام شده
- [ ] تنظیمات امنیتی اعمال شده
- [ ] مانیتورینگ راه‌اندازی شده
- [ ] Backup خودکار تنظیم شده

---

**تاریخ ایجاد:** $(date)  
**نسخه:** 1.0  
**وضعیت:** آماده برای production
