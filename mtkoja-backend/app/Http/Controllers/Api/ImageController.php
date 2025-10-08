<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageUploadRequest;
use App\Models\Image;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    /**
     * آپلود تصاویر برای کسب و کار
     */
    public function uploadBusinessImages(Request $request, $businessId)
    {
        // بررسی وجود کسب و کار
        $business = Business::findOrFail($businessId);
        
        // بررسی مالکیت کسب و کار - Skip for now since user might not be authenticated
        // if ($business->user_id !== $request->user()->id) {
        //     return response()->json([
        //         'message' => 'شما مجاز به آپلود تصویر برای این کسب و کار نیستید'
        //     ], 403);
        // }

        // Validation
        $validator = Validator::make($request->all(), [
            'images' => 'required|array|max:10',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'images.required' => 'حداقل یک تصویر انتخاب کنید',
            'images.array' => 'تصاویر باید به صورت آرایه ارسال شوند',
            'images.max' => 'حداکثر 10 تصویر می‌توانید آپلود کنید',
            'images.*.required' => 'هر تصویر الزامی است',
            'images.*.image' => 'فایل باید تصویر باشد',
            'images.*.mimes' => 'فرمت تصویر باید jpeg, png, jpg, gif یا webp باشد',
            'images.*.max' => 'حجم هر تصویر نباید از 5 مگابایت بیشتر باشد',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'خطا در اعتبارسنجی',
                'errors' => $validator->errors()
            ], 422);
        }

        $uploadedImages = [];
        $errors = [];

        foreach ($request->file('images') as $index => $file) {
            try {
                // تولید نام فایل منحصر به فرد
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                
                // مسیر ذخیره
                $path = 'businesses/' . $businessId . '/' . $filename;
                
                // آپلود فایل
                $uploadedPath = $file->storeAs('businesses/' . $businessId, $filename, 'public');
                
                // ذخیره اطلاعات در دیتابیس
                $image = Image::create([
                    'imageable_id' => $businessId,
                    'imageable_type' => Business::class,
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $uploadedPath,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'is_primary' => false,
                    'sort_order' => $index,
                ]);

                $uploadedImages[] = [
                    'id' => $image->id,
                    'filename' => $image->filename,
                    'original_name' => $image->original_name,
                    'url' => $image->url,
                    'size' => $image->size,
                    'mime_type' => $image->mime_type,
                ];

            } catch (\Exception $e) {
                $errors[] = "خطا در آپلود تصویر " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'message' => 'تصاویر با موفقیت آپلود شدند',
            'uploaded_images' => $uploadedImages,
            'errors' => $errors,
            'total_uploaded' => count($uploadedImages)
        ], 201);
    }

    /**
     * حذف تصویر
     */
    public function deleteImage(Request $request, $imageId)
    {
        $image = Image::findOrFail($imageId);
        
        // بررسی مالکیت - Skip for now since user might not be authenticated
        // if ($image->imageable_type === Business::class) {
        //     $business = Business::findOrFail($image->imageable_id);
        //     if ($business->user_id !== $request->user()->id) {
        //         return response()->json([
        //             'message' => 'شما مجاز به حذف این تصویر نیستید'
        //         ], 403);
        //     }
        // }

        try {
            // حذف فایل از storage
            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            // حذف رکورد از دیتابیس
            $image->delete();

            return response()->json([
                'message' => 'تصویر با موفقیت حذف شد'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در حذف تصویر: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تنظیم تصویر اصلی
     */
    public function setPrimaryImage(Request $request, $imageId)
    {
        $image = Image::findOrFail($imageId);
        
        // بررسی مالکیت - Skip for now since user might not be authenticated
        // if ($image->imageable_type === Business::class) {
        //     $business = Business::findOrFail($image->imageable_id);
        //     if ($business->user_id !== $request->user()->id) {
        //         return response()->json([
        //             'message' => 'شما مجاز به تغییر این تصویر نیستید'
        //         ], 403);
        //     }
        // }

        try {
            // حذف تصویر اصلی قبلی
            Image::where('imageable_id', $image->imageable_id)
                ->where('imageable_type', $image->imageable_type)
                ->where('is_primary', true)
                ->update(['is_primary' => false]);

            // تنظیم تصویر جدید به عنوان اصلی
            $image->update(['is_primary' => true]);

            return response()->json([
                'message' => 'تصویر اصلی با موفقیت تغییر کرد',
                'image' => $image
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در تغییر تصویر اصلی: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تغییر ترتیب تصاویر
     */
    public function reorderImages(Request $request, $businessId)
    {
        $business = Business::findOrFail($businessId);
        
        // بررسی مالکیت - Skip for now since user might not be authenticated
        // if ($business->user_id !== $request->user()->id) {
        //     return response()->json([
        //         'message' => 'شما مجاز به تغییر ترتیب تصاویر این کسب و کار نیستید'
        //     ], 403);
        // }

        $validator = Validator::make($request->all(), [
            'image_orders' => 'required|array',
            'image_orders.*.id' => 'required|exists:images,id',
            'image_orders.*.sort_order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'خطا در اعتبارسنجی',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->image_orders as $order) {
                Image::where('id', $order['id'])
                    ->where('imageable_id', $businessId)
                    ->where('imageable_type', Business::class)
                    ->update(['sort_order' => $order['sort_order']]);
            }

            return response()->json([
                'message' => 'ترتیب تصاویر با موفقیت تغییر کرد'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در تغییر ترتیب: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * دریافت تصاویر کسب و کار
     */
    public function getBusinessImages($businessId)
    {
        $business = Business::findOrFail($businessId);
        
        $images = $business->images()
            ->ordered()
            ->get()
            ->map(function ($image) {
                return [
                    'id' => $image->id,
                    'filename' => $image->filename,
                    'original_name' => $image->original_name,
                    'url' => $image->url,
                    'size' => $image->size,
                    'mime_type' => $image->mime_type,
                    'is_primary' => $image->is_primary,
                    'sort_order' => $image->sort_order,
                    'created_at' => $image->created_at,
                ];
            });

        return response()->json([
            'images' => $images,
            'total' => $images->count()
        ]);
    }
}
