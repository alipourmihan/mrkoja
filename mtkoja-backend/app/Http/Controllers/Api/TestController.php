<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test()
    {
        return response()->json([
            'message' => 'Test controller is working!',
            'timestamp' => now()
        ]);
    }
    
    public function testImageUpload(Request $request)
    {
        return response()->json([
            'message' => 'Image upload endpoint is accessible',
            'files_received' => $request->hasFile('images') ? count($request->file('images')) : 0,
            'all_data' => $request->all()
        ]);
    }
}
