# سیستم مدیریت تصاویر کسب و کار

## شناسه تغییر: `IMG_SYSTEM_001`

## 📁 ساختار فولدرها

```
mtkoja-backend/
├── storage/
│   └── app/
│       └── public/
│           ├── businesses/          ← تصاویر کسب و کارها
│           │   └── {business_id}/   ← فولدر مخصوص هر کسب و کار
│           └── categories/          ← تصاویر دسته‌بندی‌ها
└── public/
    └── storage/                     ← لینک سمبلیک به storage/app/public
```

## 🔧 API Endpoints

### آپلود تصاویر کسب و کار
```
POST /api/businesses/{businessId}/images
Content-Type: multipart/form-data

{
    "images": [file1, file2, ...]  // حداکثر 10 فایل
}
```

### دریافت تصاویر کسب و کار
```
GET /api/businesses/{businessId}/images
```

### حذف تصویر
```
DELETE /api/images/{imageId}
```

### تنظیم تصویر اصلی
```
PUT /api/images/{imageId}/set-primary
```

### تغییر ترتیب تصاویر
```
PUT /api/businesses/{businessId}/images/reorder
{
    "image_orders": [
        {"id": 1, "sort_order": 0},
        {"id": 2, "sort_order": 1}
    ]
}
```

## 📋 Validation Rules

- **فرمت‌های مجاز**: jpeg, png, jpg, gif, webp
- **حداکثر حجم**: 5 مگابایت
- **حداکثر تعداد**: 10 تصویر
- **حداقل تعداد**: 1 تصویر

## 🗄️ ساختار دیتابیس

### جدول images
```sql
- id (primary key)
- imageable_id (business_id)
- imageable_type (App\Models\Business)
- filename (نام فایل منحصر به فرد)
- original_name (نام اصلی فایل)
- path (مسیر ذخیره)
- mime_type (نوع فایل)
- size (حجم فایل)
- is_primary (تصویر اصلی)
- sort_order (ترتیب نمایش)
- created_at, updated_at
```

## 🔐 سطوح دسترسی

- **مالک کسب و کار**: آپلود، حذف، تغییر ترتیب تصاویر کسب و کار خود
- **ادمین**: دسترسی کامل به همه تصاویر
- **کاربران عادی**: فقط مشاهده تصاویر

## 🚀 نحوه استفاده

### 1. آپلود تصاویر
```javascript
const formData = new FormData();
formData.append('images[]', file1);
formData.append('images[]', file2);

fetch('/api/businesses/1/images', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token
    },
    body: formData
});
```

### 2. دریافت تصاویر
```javascript
fetch('/api/businesses/1/images', {
    headers: {
        'Authorization': 'Bearer ' + token
    }
})
.then(response => response.json())
.then(data => {
    console.log(data.images);
});
```

## ⚙️ تنظیمات

### فایل config/filesystems.php
```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],
```

### ایجاد symbolic link
```bash
php artisan storage:link
```

## 🔄 Migration و Seeder

### اجرای migration
```bash
php artisan migrate
```

### اجرای seeder
```bash
php artisan db:seed --class=ImageSeeder
```

## 📝 نکات مهم

1. **امنیت**: فقط مالک کسب و کار می‌تواند تصاویر آن را مدیریت کند
2. **بهینه‌سازی**: تصاویر به صورت خودکار با UUID نام‌گذاری می‌شوند
3. **مدیریت حافظه**: فایل‌های حذف شده از storage نیز پاک می‌شوند
4. **Backup**: قبل از تغییرات، backup از دیتابیس تهیه کنید

## 🐛 عیب‌یابی

### مشکل: تصاویر نمایش داده نمی‌شوند
- بررسی کنید symbolic link ایجاد شده باشد: `php artisan storage:link`
- بررسی کنید فولدر storage/app/public وجود داشته باشد
- بررسی کنید دسترسی‌های فولدر صحیح باشد

### مشکل: آپلود تصاویر کار نمی‌کند
- بررسی کنید حجم فایل از 5MB کمتر باشد
- بررسی کنید فرمت فایل مجاز باشد
- بررسی کنید token احراز هویت معتبر باشد
