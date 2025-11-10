<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BusinessController extends Controller
{
    /**
     * Display a listing of businesses
     */
    public function index(Request $request): JsonResponse
    {
        $query = Business::query();
        
        // Apply filters
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }
        
        if ($request->has('search')) {
            $query->search($request->search);
        }
        
        if ($request->has('min_rating')) {
            $query->minRating($request->min_rating);
        }
        
        // Pagination
        $perPage = $request->get('per_page', 10);
        $businesses = $query->paginate($perPage);
        
        return response()->json([
            'data' => $businesses->items(),
            'total' => $businesses->total(),
            'page' => $businesses->currentPage(),
            'per_page' => $businesses->perPage(),
            'total_pages' => $businesses->lastPage(),
        ]);
    }
    
    /**
     * Display the specified business
     */
    public function show(Business $business): JsonResponse
    {
        return response()->json($business);
    }
    
    /**
     * Get business statistics
     */
    public function stats(): JsonResponse
    {
        $total = Business::count();
        
        $categories = Business::selectRaw('category, COUNT(*) as count')
            ->whereNotNull('category')
            ->groupBy('category')
            ->pluck('count', 'category');
        
        $lastScraped = Business::max('scraped_at');
        
        return response()->json([
            'total_businesses' => $total,
            'categories' => $categories,
            'last_scraped' => $lastScraped,
        ]);
    }
    
    /**
     * Get all categories
     */
    public function categories(): JsonResponse
    {
        $categories = Business::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');
        
        return response()->json($categories);
    }
}
