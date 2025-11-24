<?php

namespace App\Models\Listing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Listing\ListingContent;
use App\Models\User;

class Listing extends Model
{
    use HasFactory;
    protected $fillable = [
        'feature_image',
        'user_id',
        'mail',
        'phone',
        'mobile_phone',
        'landline_phone',
        'website',
        'instagram',
        'telegram',
        'whatsapp',
        'average_rating',
        'latitude',
        'longitude',
        'video_url',
        'video_background_image',
        'status',
        'visibility'
    ];

    public function listing_content()
    {
        return $this->hasMany(ListingContent::class, 'listing_id', 'id');
    }

    public function galleries()
    {
        return $this->hasMany(ListingImage::class, 'listing_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @deprecated Use user() instead. Kept for backward compatibility.
     */
    public function vendor()
    {
        return $this->user();
    }
    public function listingProducts()
    {
        return $this->hasMany(ListingProduct::class, 'listing_id', 'id');
    }
    public function listingFaqs()
    {
        return $this->hasMany(ListingFaq::class);
    }
    public function specifications()
    {
        return $this->hasMany(ListingFeature::class, 'listing_id', 'id');
    }
    public function sociallinks()
    {
        return $this->hasMany(ListingSocialMedia::class, 'listing_id', 'id');
    }

    /**
     * Get the categories that belong to this listing (many-to-many relationship).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(
            \App\Models\ListingCategory::class,
            'listing_listing_category',
            'listing_id',
            'listing_category_id'
        )->withTimestamps();
    }
}
