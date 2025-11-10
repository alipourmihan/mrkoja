# مثال‌های تست API سیستم تصاویر

## شناسه تغییر: `IMG_SYSTEM_001`

## 🔐 احراز هویت

ابتدا باید لاگین کنید و token دریافت کنید:

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "owner@mtkoja.com",
    "password": "password"
  }'
```

## 📤 آپلود تصاویر

### آپلود چند تصویر برای کسب و کار
```bash
curl -X POST http://localhost:8000/api/businesses/1/images \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "images[]=@image1.jpg" \
  -F "images[]=@image2.jpg" \
  -F "images[]=@image3.jpg"
```

### پاسخ موفق:
```json
{
  "message": "تصاویر با موفقیت آپلود شدند",
  "uploaded_images": [
    {
      "id": 1,
      "filename": "uuid-filename.jpg",
      "original_name": "image1.jpg",
      "url": "http://localhost:8000/storage/businesses/1/uuid-filename.jpg",
      "size": 245760,
      "mime_type": "image/jpeg"
    }
  ],
  "errors": [],
  "total_uploaded": 3
}
```

## 📥 دریافت تصاویر کسب و کار

```bash
curl -X GET http://localhost:8000/api/businesses/1/images \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### پاسخ:
```json
{
  "images": [
    {
      "id": 1,
      "filename": "uuid-filename.jpg",
      "original_name": "image1.jpg",
      "url": "http://localhost:8000/storage/businesses/1/uuid-filename.jpg",
      "size": 245760,
      "mime_type": "image/jpeg",
      "is_primary": true,
      "sort_order": 0,
      "created_at": "2024-01-01T12:00:00.000000Z"
    }
  ],
  "total": 1
}
```

## 🗑️ حذف تصویر

```bash
curl -X DELETE http://localhost:8000/api/images/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### پاسخ:
```json
{
  "message": "تصویر با موفقیت حذف شد"
}
```

## ⭐ تنظیم تصویر اصلی

```bash
curl -X PUT http://localhost:8000/api/images/2/set-primary \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### پاسخ:
```json
{
  "message": "تصویر اصلی با موفقیت تغییر کرد",
  "image": {
    "id": 2,
    "is_primary": true,
    "sort_order": 1
  }
}
```

## 🔄 تغییر ترتیب تصاویر

```bash
curl -X PUT http://localhost:8000/api/businesses/1/images/reorder \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "image_orders": [
      {"id": 2, "sort_order": 0},
      {"id": 1, "sort_order": 1},
      {"id": 3, "sort_order": 2}
    ]
  }'
```

### پاسخ:
```json
{
  "message": "ترتیب تصاویر با موفقیت تغییر کرد"
}
```

## 📋 دریافت کسب و کار با تصاویر

```bash
curl -X GET http://localhost:8000/api/businesses/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### پاسخ:
```json
{
  "business": {
    "id": 1,
    "name": "نام کسب و کار",
    "description": "توضیحات کسب و کار",
    "image_urls": [
      {
        "id": 1,
        "url": "http://localhost:8000/storage/businesses/1/uuid-filename.jpg",
        "is_primary": true,
        "sort_order": 0
      }
    ],
    "category": {...},
    "user": {...}
  }
}
```

## ❌ خطاهای رایج

### خطای اعتبارسنجی
```json
{
  "message": "خطا در اعتبارسنجی",
  "errors": {
    "images.0": ["فایل باید تصویر باشد"],
    "images.1": ["حجم هر تصویر نباید از 5 مگابایت بیشتر باشد"]
  }
}
```

### خطای دسترسی
```json
{
  "message": "شما مجاز به آپلود تصویر برای این کسب و کار نیستید"
}
```

### خطای عدم وجود
```json
{
  "message": "Business not found"
}
```

## 🧪 تست با Postman

1. **Collection ایجاد کنید** با نام "MTKoja Image API"
2. **Environment تنظیم کنید** با متغیر `base_url` = `http://localhost:8000/api`
3. **Authorization** را روی Bearer Token تنظیم کنید
4. **Headers** اضافه کنید: `Accept: application/json`

## 📱 تست با JavaScript (Frontend)

```javascript
// آپلود تصاویر
async function uploadImages(businessId, files) {
  const formData = new FormData();
  files.forEach(file => {
    formData.append('images[]', file);
  });

  const response = await fetch(`/api/businesses/${businessId}/images`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`
    },
    body: formData
  });

  return await response.json();
}

// دریافت تصاویر
async function getBusinessImages(businessId) {
  const response = await fetch(`/api/businesses/${businessId}/images`, {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });

  return await response.json();
}

// حذف تصویر
async function deleteImage(imageId) {
  const response = await fetch(`/api/images/${imageId}`, {
    method: 'DELETE',
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });

  return await response.json();
}
```

## 🔧 تنظیمات سرور

### Apache (.htaccess)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^storage/(.*)$ storage/app/public/$1 [L]
</IfModule>
```

### Nginx
```nginx
location /storage {
    alias /path/to/project/storage/app/public;
    try_files $uri $uri/ =404;
}
```
