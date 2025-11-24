<?php

namespace App\Models\Listing;

use App\Models\ListingCategory;
use App\Models\Location\City;
use App\Models\Location\Neighborhood;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'language_id',
        'listing_id',
        'category_id',
        'country_id',
        'state_id',
        'city_id',
        'neighborhood_id',
        'title',
        'slug',
        'short_description',
        'description',
        'address',
        'meta_keyword',
        'meta_description',
        'features',
        'aminities'
    ];

    public function category()
    {
        return $this->belongsTo(ListingCategory::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
