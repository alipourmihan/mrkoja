#!/bin/bash
# تست سیستم تصاویر با curl

echo "=== تست سیستم تصاویر MTKoja ==="

# 1. تست دریافت تصاویر
echo "1. تست دریافت تصاویر کسب و کار..."
curl -X GET "http://localhost:8000/api/businesses/1/images" \
  -H "Accept: application/json" \
  -w "\nHTTP Status: %{http_code}\n"

echo -e "\n2. تست کنترلر ساده..."
curl -X GET "http://localhost:8000/api/simple/businesses/1/images" \
  -H "Accept: application/json" \
  -w "\nHTTP Status: %{http_code}\n"

echo -e "\n3. تست آپلود (بدون فایل)..."
curl -X POST "http://localhost:8000/api/businesses/1/images" \
  -H "Accept: application/json" \
  -w "\nHTTP Status: %{http_code}\n"

echo -e "\n=== پایان تست ==="
