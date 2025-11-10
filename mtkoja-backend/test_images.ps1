# تست سیستم تصاویر با PowerShell
Write-Host "=== تست سیستم تصاویر MTKoja ===" -ForegroundColor Green

# 1. تست دریافت تصاویر کسب و کار
Write-Host "`n1. تست دریافت تصاویر کسب و کار..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000/api/businesses/1/images" -Method GET
    Write-Host "✅ موفق: دریافت تصاویر" -ForegroundColor Green
    Write-Host "پاسخ: $($response.Content)"
} catch {
    Write-Host "❌ خطا: $($_.Exception.Message)" -ForegroundColor Red
}

# 2. تست آپلود تصویر (بدون فایل واقعی)
Write-Host "`n2. تست آپلود تصویر..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000/api/businesses/1/images" -Method POST
    Write-Host "✅ موفق: آپلود تصویر" -ForegroundColor Green
    Write-Host "پاسخ: $($response.Content)"
} catch {
    Write-Host "❌ خطا: $($_.Exception.Message)" -ForegroundColor Red
}

# 3. تست کنترلر ساده
Write-Host "`n3. تست کنترلر ساده..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000/api/simple/businesses/1/images" -Method GET
    Write-Host "✅ موفق: کنترلر ساده" -ForegroundColor Green
    Write-Host "پاسخ: $($response.Content)"
} catch {
    Write-Host "❌ خطا: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`n=== پایان تست ===" -ForegroundColor Green
