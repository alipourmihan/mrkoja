<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DebugController extends Controller
{
    public function test()
    {
        return response()->json([
            'message' => 'Debug controller is working!',
            'timestamp' => now(),
            'php_version' => PHP_VERSION
        ]);
    }
    
    public function testBusiness($id)
    {
        try {
            $business = \App\Models\Business::find($id);
            
            if (!$business) {
                return response()->json([
                    'message' => 'کسب و کار یافت نشد',
                    'id' => $id
                ], 404);
            }
            
            return response()->json([
                'message' => 'کسب و کار یافت شد',
                'business' => [
                    'id' => $business->id,
                    'name' => $business->name,
                    'description' => $business->description
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در دریافت کسب و کار',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
