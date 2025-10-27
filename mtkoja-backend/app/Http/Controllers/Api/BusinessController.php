<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Category;
use App\Models\Review;
use App\Models\Image;
use App\Models\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;

class BusinessController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Business::query()->with(['category', 'user', 'images'])
            ->approved();

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search by name or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort by rating or created_at
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if ($sortBy === 'rating') {
            $query->byRating();
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = $request->get('per_page', 12);
        $businesses = $query->paginate($perPage);

        // Add image URLs to each business
        $businesses->getCollection()->transform(function ($business) {
            // Get images from database using relationship
            $images = $business->images()->ordered()->get();

            $business->image_urls = $images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->url,
                    'is_primary' => $image->is_primary,
                    'sort_order' => $image->sort_order,
                ];
            })->values()->all();

            // Ensure empty array if no images
            if (!$business->image_urls) {
                $business->image_urls = [];
            }

            return $business;
        });

        return Response::json([
            'businesses' => $businesses
        ]);
    }

    public function show($identifier): JsonResponse
    {
        // Try to find by ID first, then by slug
        $business = Business::query()->where('id', $identifier)
            ->orWhere('slug', $identifier)
            ->with(['category', 'user', 'images'])
            ->first();
            
        if (!$business) {
            return Response::json([
                'message' => 'Business not found'
            ], 404);
        }
        
        // Add image URLs to business data
        $images = $business->images()->ordered()->get();

        $business->image_urls = $images->map(function ($image) {
            return [
                'id' => $image->id,
                'url' => $image->url,
                'is_primary' => $image->is_primary,
                'sort_order' => $image->sort_order,
            ];
        })->values()->all();
        
        return Response::json([
            'business' => $business
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'fullDescription' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'landline' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'telegram' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'province_id' => 'nullable|exists:provinces,id',
            'city_id' => 'nullable|exists:cities,id',
            'neighborhood_id' => 'nullable|exists:neighborhoods,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'keywords' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'string',
            'features' => 'nullable|array',
            'working_hours' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return Response::json([
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get user ID safely - do NOT create default users
        if (!$request->user()) {
            return Response::json([
                'message' => 'Authentication required'
            ], 401);
        }
        $userId = $request->user()->id;

        $business = Business::query()->create([
            'user_id' => $userId,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'description' => $request->description,
            'full_description' => $request->fullDescription ?? $request->description,
            'address' => $request->address ?? '',
            'phone' => $request->phone ?? '',
            'landline' => $request->landline ?? '',
            'email' => $request->email ?? '',
            'website' => $request->website ?? '',
            'instagram' => $request->instagram ?? '',
            'whatsapp' => $request->whatsapp ?? '',
            'telegram' => $request->telegram ?? '',
            'latitude' => $request->latitude ?? null,
            'longitude' => $request->longitude ?? null,
            'province_id' => $request->province_id ?? null,
            'city_id' => $request->city_id ?? null,
            'neighborhood_id' => $request->neighborhood_id ?? null,
            'meta_title' => $request->meta_title ?? '',
            'meta_description' => $request->meta_description ?? '',
            'keywords' => $request->keywords ?? '',
            'images' => $request->images ?? [],
            'features' => $request->features ?? [],
            'working_hours' => $request->working_hours ?? [],
            'status' => 'pending', // Explicitly set status to pending
        ]);

        // Load relationships safely
        try {
            $business->load(['category', 'user']);
        } catch (\Exception $e) {
            \Log::warning('Failed to load business relationships: ' . $e->getMessage());
        }

        return Response::json([
            'message' => 'Business created successfully',
            'business' => $business
        ], 201);
    }

    public function update(Request $request, Business $business): JsonResponse
    {
        // Check if user owns this business
        if ($business->user_id !== $request->user()->id) {
            return Response::json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'landline' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'instagram' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'telegram' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'images' => 'nullable|array',
            'images.*' => 'string',
            'features' => 'nullable|array',
            'working_hours' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return Response::json([
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $business->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'description' => $request->description,
            'address' => $request->address,
            'phone' => $request->phone,
            'landline' => $request->landline,
            'email' => $request->email,
            'website' => $request->website,
            'instagram' => $request->instagram,
            'whatsapp' => $request->whatsapp,
            'telegram' => $request->telegram,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'images' => $request->images,
            'features' => $request->features,
            'working_hours' => $request->working_hours,
        ]);

        $business->load(['category', 'user']);

        return Response::json([
            'message' => 'Business updated successfully',
            'business' => $business
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $business = Business::query()->findOrFail($id);
        $business->delete();

        return Response::json([
            'message' => 'Business deleted successfully'
        ]);
    }

    public function myBusinesses(Request $request): JsonResponse
    {
        try {
            // Get user ID safely
            $userId = 1; // Default user
            if ($request->user()) {
                $userId = $request->user()->id;
            } else {
                // Get the first user if no authenticated user
                $defaultUser = \App\Models\User::first();
                if ($defaultUser) {
                    $userId = $defaultUser->id;
                }
            }

            $query = Business::query()->with(['category', 'images'])
                ->where('user_id', $userId);

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Search by name or description
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Sort by created_at desc by default
            $query->orderBy('created_at', 'desc');

            $perPage = $request->get('per_page', 12);
            $businesses = $query->paginate($perPage);

            // Add image URLs to each business
            $businesses->getCollection()->transform(function ($business) {
                if ($business->images && $business->images->count() > 0) {
                    $business->images = $business->images->map(function ($image) {
                        $image->url = $image->url;
                        return $image;
                    });
                }
                return $business;
            });

            return Response::json([
                'businesses' => $businesses
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching user businesses: ' . $e->getMessage());
            return Response::json([
                'message' => 'Error fetching businesses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $query = Business::query()->with(['category', 'user', 'images']);

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Search by name, description, or address
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%");
                });
            }

            // Filter by category
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Sort by created_at desc by default
            $query->orderBy('created_at', 'desc');

            $perPage = $request->get('per_page', 20);
            $businesses = $query->paginate($perPage);

            // Add image URLs to each business
            $businesses->getCollection()->transform(function ($business) {
                if ($business->images && $business->images->count() > 0) {
                    $business->images = $business->images->map(function ($image) {
                        $image->url = $image->url;
                        return $image;
                    });
                }
                return $business;
            });

            return Response::json([
                'businesses' => $businesses
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching businesses for admin: ' . $e->getMessage());
            return Response::json([
                'message' => 'Error fetching businesses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function approve($id): JsonResponse
    {
        $business = Business::query()->findOrFail($id);
        $business->update(['status' => 'approved']);

        return Response::json([
            'message' => 'Business approved successfully',
            'business' => $business
        ]);
    }

    public function reject($id): JsonResponse
    {
        $business = Business::query()->findOrFail($id);
        $business->update(['status' => 'rejected']);

        return Response::json([
            'message' => 'Business rejected successfully',
            'business' => $business
        ]);
    }

    public function adminStore(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'address' => 'nullable|string|max:500',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|string|max:255',
                'instagram' => 'nullable|string|max:255',
                'whatsapp' => 'nullable|string|max:20',
                'telegram' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'province_id' => 'nullable|exists:provinces,id',
                'city_id' => 'nullable|exists:cities,id',
                'neighborhood_id' => 'nullable|exists:neighborhoods,id',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'keywords' => 'nullable|string',
                'features' => 'nullable|array',
                'working_hours' => 'nullable|array',
                'status' => 'nullable|in:pending,approved,rejected',
                'is_featured' => 'nullable|boolean',
                'user_id' => 'nullable|exists:users,id'
            ]);

            if ($validator->fails()) {
                return Response::json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validate category exists
            $category = Category::query()->find($request->category_id);
            if (!$category) {
                return Response::json([
                    'message' => 'Category not found',
                    'error' => 'Invalid category_id'
                ], 404);
            }

            // Get user ID - use provided user_id or authenticated user or create a default user
            $userId = $request->user_id;
            if (!$userId && $request->user()) {
                $userId = $request->user()->id;
            }
            if (!$userId) {
                // Create a default user if none exists
                $defaultUser = \App\Models\User::first();
                if (!$defaultUser) {
                    $defaultUser = \App\Models\User::create([
                        'name' => 'Default User',
                        'email' => 'default@example.com',
                        'password' => Hash::make('password'),
                        'role' => 'admin'
                    ]);
                }
                $userId = $defaultUser->id;
            }

            $business = Business::query()->create([
                'user_id' => $userId,
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'full_description' => $request->fullDescription ?? $request->description,
                'address' => $request->address ?? '',
                'phone' => $request->phone ?? '',
                'email' => $request->email ?? '',
                'website' => $request->website ?? '',
                'instagram' => $request->instagram ?? '',
                'whatsapp' => $request->whatsapp ?? '',
                'telegram' => $request->telegram ?? '',
                'latitude' => $request->latitude ?? null,
                'longitude' => $request->longitude ?? null,
                'province_id' => $request->province_id ?? null,
                'city_id' => $request->city_id ?? null,
                'neighborhood_id' => $request->neighborhood_id ?? null,
                'meta_title' => $request->meta_title ?? '',
                'meta_description' => $request->meta_description ?? '',
                'keywords' => $request->keywords ?? '',
                'features' => $request->features ?? [],
                'working_hours' => $request->working_hours ?? [],
                'status' => $request->status ?? 'pending',
                'is_featured' => $request->boolean('is_featured'),
            ]);

            // Load relationships safely
            try {
                $business->load(['category', 'user']);
            } catch (\Exception $e) {
                // If loading relationships fails, continue without them
                \Log::warning('Failed to load business relationships: ' . $e->getMessage());
            }

            return Response::json([
                'message' => 'Business created successfully',
                'business' => $business
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Error creating business: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return Response::json([
                'message' => 'Error creating business: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function adminShow($id): JsonResponse
    {
        $business = Business::query()->with(['category', 'user', 'images'])
            ->findOrFail($id);

        // Add image URLs
        $business = $this->addImageUrls($business);

        return Response::json([
            'business' => $business
        ]);
    }

    public function adminUpdate(Request $request, $id): JsonResponse
    {
        $business = Business::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'nullable|in:pending,approved,rejected',
            'is_featured' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return Response::json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $business->update($request->only([
            'name', 'description', 'category_id', 'address', 'phone', 
            'email', 'website', 'latitude', 'longitude', 'status', 'is_featured'
        ]));

        // Add image URLs
        $business = $this->addImageUrls($business);

        return Response::json([
            'message' => 'Business updated successfully',
            'business' => $business
        ]);
    }

    public function stats(): JsonResponse
    {
        // For testing purposes, return dummy stats
        $stats = [
            'total_businesses' => 0,
            'approved_businesses' => 0,
            'pending_businesses' => 0,
            'rejected_businesses' => 0,
            'featured_businesses' => 0,
            'total_categories' => 0,
            'active_categories' => 0,
        ];

        return Response::json([
            'stats' => $stats
        ]);
    }

    /**
     * اضافه کردن URL های تصاویر به داده کسب و کار
     */
    private function addImageUrls($business)
    {
        if ($business->images) {
            $business->image_urls = $business->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->url,
                    'is_primary' => $image->is_primary,
                    'sort_order' => $image->sort_order,
                ];
            })->values()->all();
        } else {
            $business->image_urls = [];
        }
        
        return $business;
    }
}
