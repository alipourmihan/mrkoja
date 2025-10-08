<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;

class SimpleImageController extends Controller
{
    /**
     * دریافت تصاویر کسب و کار (ساده)
     */
    public function getBusinessImages($businessId)
    {
        try {
            $business = Business::findOrFail($businessId);
            
            return response()->json([
                'message' => 'تصاویر کسب و کار',
                'business_id' => $businessId,
                'business_name' => $business->name,
                'images' => [],
                'total' => 0
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در دریافت تصاویر',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * تست آپلود تصاویر
     */
    public function testUpload(Request $request, $businessId)
    {
        try {
            $business = Business::findOrFail($businessId);
            
            $files = $request->file('images', []);
            
            return response()->json([
                'message' => 'تست آپلود موفق',
                'business_id' => $businessId,
                'files_received' => count($files),
                'business_name' => $business->name
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در تست آپلود',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
