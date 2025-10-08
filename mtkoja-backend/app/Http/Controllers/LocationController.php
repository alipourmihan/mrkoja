<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\City;
use App\Models\Neighborhood;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Get all provinces
     */
    public function getProvinces(): JsonResponse
    {
        try {
            // Get all provinces from database
            $provinces = Province::all();

            return response()->json([
                'provinces' => $provinces
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'خطا در بارگذاری استان‌ها',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cities by province
     */
    public function getCitiesByProvince(Province $province): JsonResponse
    {
        $cities = $province->cities()
            ->active()
            ->ordered()
            ->get();

        return response()->json([
            'cities' => $cities
        ]);
    }

    /**
     * Get neighborhoods by city
     */
    public function getNeighborhoodsByCity(City $city): JsonResponse
    {
        $neighborhoods = $city->neighborhoods()
            ->active()
            ->ordered()
            ->get();

        return response()->json([
            'neighborhoods' => $neighborhoods
        ]);
    }

    /**
     * Get all cities
     */
    public function getCities(): JsonResponse
    {
        try {
            // Get all cities from database
            $cities = City::with(['province'])->get();

            return response()->json([
                'cities' => $cities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'خطا در بارگذاری شهرها',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all neighborhoods
     */
    public function getNeighborhoods(): JsonResponse
    {
        try {
            // Get all neighborhoods from database
            $neighborhoods = Neighborhood::with(['city.province'])->get();

            return response()->json([
                'neighborhoods' => $neighborhoods
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'خطا در بارگذاری محله‌ها',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== PROVINCES CRUD ====================
    
    /**
     * Create a new province
     */
    public function createProvince(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:provinces,slug',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $province = Province::create($request->all());

        return response()->json([
            'message' => 'استان با موفقیت ایجاد شد',
            'province' => $province
        ], 201);
    }

    /**
     * Update a province
     */
    public function updateProvince(Request $request, Province $province): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:provinces,slug,' . $province->id,
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $province->update($request->all());

        return response()->json([
            'message' => 'استان با موفقیت به‌روزرسانی شد',
            'province' => $province
        ]);
    }

    /**
     * Delete a province
     */
    public function deleteProvince($id): JsonResponse
    {
        $province = Province::findOrFail($id);
        $province->delete();

        return response()->json([
            'message' => 'استان با موفقیت حذف شد'
        ]);
    }

    // ==================== CITIES CRUD ====================
    
    /**
     * Create a new city
     */
    public function createCity(Request $request): JsonResponse
    {
        $request->validate([
            'province_id' => 'required|exists:provinces,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cities,slug',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $city = City::create($request->all());

        return response()->json([
            'message' => 'شهر با موفقیت ایجاد شد',
            'city' => $city->load('province')
        ], 201);
    }

    /**
     * Update a city
     */
    public function updateCity(Request $request, City $city): JsonResponse
    {
        $request->validate([
            'province_id' => 'required|exists:provinces,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cities,slug,' . $city->id,
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $city->update($request->all());

        return response()->json([
            'message' => 'شهر با موفقیت به‌روزرسانی شد',
            'city' => $city->load('province')
        ]);
    }

    /**
     * Delete a city
     */
    public function deleteCity($id): JsonResponse
    {
        $city = City::findOrFail($id);
        $city->delete();

        return response()->json([
            'message' => 'شهر با موفقیت حذف شد'
        ]);
    }

    // ==================== NEIGHBORHOODS CRUD ====================
    
    /**
     * Create a new neighborhood
     */
    public function createNeighborhood(Request $request): JsonResponse
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:neighborhoods,slug',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $neighborhood = Neighborhood::create($request->all());

        return response()->json([
            'message' => 'محله با موفقیت ایجاد شد',
            'neighborhood' => $neighborhood->load('city.province')
        ], 201);
    }

    /**
     * Update a neighborhood
     */
    public function updateNeighborhood(Request $request, Neighborhood $neighborhood): JsonResponse
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:neighborhoods,slug,' . $neighborhood->id,
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $neighborhood->update($request->all());

        return response()->json([
            'message' => 'محله با موفقیت به‌روزرسانی شد',
            'neighborhood' => $neighborhood->load('city.province')
        ]);
    }

    /**
     * Delete a neighborhood
     */
    public function deleteNeighborhood($id): JsonResponse
    {
        $neighborhood = Neighborhood::findOrFail($id);
        $neighborhood->delete();

        return response()->json([
            'message' => 'محله با موفقیت حذف شد'
        ]);
    }

    // ==================== CATEGORIES CRUD ====================
    
    /**
     * Get all categories with parent-child relationships (for admin panel)
     */
    public function getCategories(): JsonResponse
    {
        // Get all categories from database for admin panel
        $categories = Category::with('parent')
            ->ordered()
            ->get();

        return response()->json([
            'categories' => $categories
        ]);
    }

    /**
     * Get public categories (only active ones)
     */
    public function getPublicCategories(): JsonResponse
    {
        // Get only active categories for public use
        $categories = Category::with('parent')
            ->active()
            ->ordered()
            ->get();

        return response()->json([
            'categories' => $categories
        ]);
    }

    /**
     * Create a new category
     */
    public function createCategory(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $category = Category::create($request->all());

        return response()->json([
            'message' => 'دسته‌بندی با موفقیت ایجاد شد',
            'category' => $category->load('parent')
        ], 201);
    }

    /**
     * Update a category
     */
    public function updateCategory(Request $request, Category $category): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $category->update($request->all());

        return response()->json([
            'message' => 'دسته‌بندی با موفقیت به‌روزرسانی شد',
            'category' => $category->load('parent')
        ]);
    }

    /**
     * Delete a category
     */
    public function deleteCategory(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'message' => 'دسته‌بندی با موفقیت حذف شد'
        ]);
    }
}