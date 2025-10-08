<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'address',
        'phone',
        'landline',
        'email',
        'website',
        'instagram',
        'whatsapp',
        'telegram', // Added
        'category_id',
        'user_id',
        'province_id',
        'city_id',
        'neighborhood_id',
        'latitude',
        'longitude',
        'rating',
        'reviews_count',
        'views_count',
        'status',
        'is_active',
        'is_featured',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'twitter_title',
        'twitter_description',
        'canonical_url',
        'working_hours',
        'social_media',
        'is_indexed',
        'is_followed',
    ];
    
    protected $casts = [
        'rating' => 'decimal:1',
        'reviews_count' => 'integer',
        'views_count' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'working_hours' => 'array',
        'social_media' => 'array',
        'is_indexed' => 'boolean',
        'is_followed' => 'boolean',
    ];

    /**
     * Get the category that owns the business.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user that owns the business.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the province that owns the business.
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Get the city that owns the business.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the neighborhood that owns the business.
     */
    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }

    /**
     * Get the images for the business.
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the reviews for the business.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the views for the business.
     */
    public function views()
    {
        return $this->hasMany(View::class);
    }
    
    /**
     * Scope for filtering by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', 'like', "%{$category}%");
    }
    
    /**
     * Scope for searching in name and description
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }
    
    /**
     * Scope for minimum rating
     */
    public function scopeMinRating($query, float $rating)
    {
        return $query->where('rating', '>=', $rating);
    }

    /**
     * Scope for sorting by rating
     */
    public function scopeByRating($query)
    {
        return $query->orderBy('rating', 'desc');
    }

    /**
     * Scope for approved businesses
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for active businesses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured businesses
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}