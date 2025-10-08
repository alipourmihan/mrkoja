<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\SimpleImageController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SeoPageController;
use App\Http\Controllers\SeoManagerController;
use App\Http\Controllers\Admin\SeoAdminController;
use App\Http\Controllers\LocationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Test routes
Route::get('/test', [TestController::class, 'test']);
Route::post('/test-image-upload', [TestController::class, 'testImageUpload']);

// Debug routes
Route::get('/debug', [\App\Http\Controllers\Api\DebugController::class, 'test']);
Route::get('/debug/business/{id}', [\App\Http\Controllers\Api\DebugController::class, 'testBusiness']);

// Simple image routes (for testing)
Route::get('/simple/businesses/{businessId}/images', [SimpleImageController::class, 'getBusinessImages']);
Route::post('/simple/businesses/{businessId}/images', [SimpleImageController::class, 'testUpload']);

// Public routes
Route::get('/categories', [LocationController::class, 'getPublicCategories']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::get('/businesses', [BusinessController::class, 'index']);
Route::get('/businesses/{business}', [BusinessController::class, 'show']);

// Scraper business routes
Route::get('/scraper/businesses', [\App\Http\Controllers\BusinessController::class, 'index']);
Route::get('/scraper/businesses/{business}', [\App\Http\Controllers\BusinessController::class, 'show']);
Route::get('/scraper/stats', [\App\Http\Controllers\BusinessController::class, 'stats']);
Route::get('/scraper/categories', [\App\Http\Controllers\BusinessController::class, 'categories']);

// SEO Routes - Business listings with location-based URLs
Route::prefix('b')->group(function () {
    // /b/{category} - All country
    Route::get('/{category}', [SeoController::class, 'showCategory']);
    
    // /b/{category}/{city} - By city
    Route::get('/{category}/{city}', [SeoController::class, 'showCategoryByCity']);
    
    // /b/{category}/{city}/{neighborhood} - By neighborhood
    Route::get('/{category}/{city}/{neighborhood}', [SeoController::class, 'showCategoryByNeighborhood']);
});

// Business profile routes
Route::get('/business/{business}', [SeoController::class, 'showBusiness']);

// SEO Management routes
Route::prefix('seo')->group(function () {
    Route::get('/sitemap/generate', [SeoManagerController::class, 'generateSitemap']);
    Route::get('/robots/generate', [SeoManagerController::class, 'generateRobotsTxt']);
    Route::get('/homepage/schema', [SeoManagerController::class, 'generateHomepageSchema']);
    Route::post('/validate', [SeoManagerController::class, 'validateSeo']);
    Route::get('/stats', [SeoManagerController::class, 'getSeoStats']);
});

// Location routes
Route::get('/provinces', [LocationController::class, 'getProvinces']);
Route::get('/provinces/{province}/cities', [LocationController::class, 'getCitiesByProvince']);
Route::get('/cities', [LocationController::class, 'getCities']);
Route::get('/cities/{city}/neighborhoods', [LocationController::class, 'getNeighborhoodsByCity']);
Route::get('/neighborhoods', [LocationController::class, 'getNeighborhoods']);

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes - temporarily without authentication for testing
Route::group([], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Business owner routes - Allow all authenticated users to create businesses
    Route::post('/businesses', [BusinessController::class, 'store']);
    Route::put('/businesses/{business}', [BusinessController::class, 'update']);
    Route::delete('/businesses/{business}', [BusinessController::class, 'destroy']);
    Route::get('/my-businesses', [BusinessController::class, 'myBusinesses']);
    
    // Image management routes (without role middleware for testing)
    Route::post('/businesses/{businessId}/images', [ImageController::class, 'uploadBusinessImages']);
    Route::get('/businesses/{businessId}/images', [ImageController::class, 'getBusinessImages']);
    Route::delete('/images/{imageId}', [ImageController::class, 'deleteImage']);
    Route::put('/images/{imageId}/set-primary', [ImageController::class, 'setPrimaryImage']);
    Route::put('/businesses/{businessId}/images/reorder', [ImageController::class, 'reorderImages']);
    
    // All authenticated users can view their businesses
    Route::get('/my-businesses', [BusinessController::class, 'myBusinesses']);
    
    // Admin routes - temporarily without role middleware for debugging
    Route::group([], function () {
        // Categories management
        Route::get('/admin/categories', [LocationController::class, 'getCategories']);
        Route::post('/categories', [LocationController::class, 'createCategory']);
        Route::put('/categories/{category}', [LocationController::class, 'updateCategory']);
        Route::delete('/categories/{category}', [LocationController::class, 'deleteCategory']);
        
        // Provinces management
        Route::post('/provinces', [LocationController::class, 'createProvince']);
        Route::put('/provinces/{province}', [LocationController::class, 'updateProvince']);
        Route::delete('/provinces/{id}', [LocationController::class, 'deleteProvince']);
        
        // Cities management
        Route::post('/cities', [LocationController::class, 'createCity']);
        Route::put('/cities/{city}', [LocationController::class, 'updateCity']);
        Route::delete('/cities/{id}', [LocationController::class, 'deleteCity']);
        
        // Neighborhoods management
        Route::post('/neighborhoods', [LocationController::class, 'createNeighborhood']);
        Route::put('/neighborhoods/{neighborhood}', [LocationController::class, 'updateNeighborhood']);
        Route::delete('/neighborhoods/{id}', [LocationController::class, 'deleteNeighborhood']);
        
        // Business management
        Route::get('/admin/businesses', [BusinessController::class, 'adminIndex']);
        Route::post('/admin/businesses', [BusinessController::class, 'adminStore']);
        Route::get('/admin/businesses/{id}', [BusinessController::class, 'adminShow']);
        Route::put('/admin/businesses/{id}', [BusinessController::class, 'adminUpdate']);
        Route::delete('/admin/businesses/{id}', [BusinessController::class, 'destroy']);
        Route::put('/admin/businesses/{id}/approve', [BusinessController::class, 'approve']);
        Route::put('/admin/businesses/{id}/reject', [BusinessController::class, 'reject']);
        Route::get('/admin/stats', [BusinessController::class, 'stats']);
        // User management
        Route::get('/admin/users', [AuthController::class, 'getAllUsers']);
        Route::post('/admin/users', [AuthController::class, 'createUser']);
        Route::get('/admin/users/{user}', [AuthController::class, 'getUser']);
        Route::put('/admin/users/{user}', [AuthController::class, 'updateUser']);
        Route::delete('/admin/users/{user}', [AuthController::class, 'deleteUser']);
        Route::get('/admin/users/{user}/businesses', [AuthController::class, 'getUserBusinesses']);

        // SEO Pages management
        Route::get('/admin/seo-pages', [SeoPageController::class, 'index']);
        Route::post('/admin/seo-pages', [SeoPageController::class, 'store']);
        Route::get('/admin/seo-pages/{seoPage}', [SeoPageController::class, 'show']);
        Route::put('/admin/seo-pages/{seoPage}', [SeoPageController::class, 'update']);
        Route::delete('/admin/seo-pages/{seoPage}', [SeoPageController::class, 'destroy']);

        // SEO Management
        Route::get('/admin/seo/dashboard', [SeoAdminController::class, 'dashboard']);
        Route::get('/admin/seo/pages', [SeoAdminController::class, 'getAllSeoPages']);
        Route::get('/admin/seo/page-details', [SeoAdminController::class, 'getSeoPageDetails']);
        Route::put('/admin/seo/update-page', [SeoAdminController::class, 'updateSeoPage']);
        Route::get('/admin/seo/reports', [SeoAdminController::class, 'getSeoReports']);
        Route::get('/admin/seo/businesses-without-seo', [SeoAdminController::class, 'businessesWithoutSeo']);
        Route::get('/admin/seo/categories-without-seo', [SeoAdminController::class, 'categoriesWithoutSeo']);
        Route::post('/admin/seo/generate-business/{business}', [SeoAdminController::class, 'generateBusinessSeo']);
        Route::post('/admin/seo/generate-category/{category}', [SeoAdminController::class, 'generateCategorySeo']);
        Route::post('/admin/seo/generate-bulk', [SeoAdminController::class, 'generateBulkSeo']);
        Route::post('/admin/seo/validate', [SeoAdminController::class, 'validateSeo']);
        Route::post('/admin/seo/generate-sitemap', [SeoAdminController::class, 'generateSitemap']);
        Route::post('/admin/seo/generate-robots', [SeoAdminController::class, 'generateRobotsTxt']);
    });
});

