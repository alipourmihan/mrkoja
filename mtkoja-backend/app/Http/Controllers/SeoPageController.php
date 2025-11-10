<?php

namespace App\Http\Controllers;

use App\Models\SeoPage;
use App\Models\Category;
use App\Models\Province;
use App\Models\City;
use App\Models\Neighborhood;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SeoPageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $seoPages = SeoPage::with(['category', 'province', 'city', 'neighborhood'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => $seoPages
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'province_id' => 'nullable|exists:provinces,id',
                'city_id' => 'nullable|exists:cities,id',
                'neighborhood_id' => 'nullable|exists:neighborhoods,id',
                'title' => 'required|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'h1' => 'nullable|string|max:255',
                'content' => 'nullable|string',
                'og_image' => 'nullable|string|max:500',
                'custom_text' => 'nullable|string',
                'keywords' => 'nullable|string|max:500',
            ]);

            $seoPage = SeoPage::create($request->all());

            return response()->json([
                'message' => 'صفحه SEO با موفقیت ایجاد شد',
                'data' => $seoPage->load(['category', 'province', 'city', 'neighborhood'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در ایجاد صفحه SEO',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SeoPage $seoPage): JsonResponse
    {
        return response()->json([
            'data' => $seoPage->load(['category', 'province', 'city', 'neighborhood'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SeoPage $seoPage): JsonResponse
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'province_id' => 'nullable|exists:provinces,id',
            'city_id' => 'nullable|exists:cities,id',
            'neighborhood_id' => 'nullable|exists:neighborhoods,id',
            'title' => 'required|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'h1' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'og_image' => 'nullable|string|max:500',
            'custom_text' => 'nullable|string',
            'keywords' => 'nullable|string|max:500',
        ]);

        $seoPage->update($request->all());

        return response()->json([
            'message' => 'صفحه SEO با موفقیت ویرایش شد',
            'data' => $seoPage->load(['category', 'province', 'city', 'neighborhood'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SeoPage $seoPage): JsonResponse
    {
        $seoPage->delete();

        return response()->json([
            'message' => 'صفحه SEO با موفقیت حذف شد'
        ]);
    }
}