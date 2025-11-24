<?php

namespace App\Models;

use App\Models\ListingCategory;
use App\Models\Location\State;
use App\Models\Location\City;
use App\Models\Location\Neighborhood;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryLocationPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'state_id',
        'city_id',
        'neighborhood_id',
        'title',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'description',
        'featured_image',
        'status',
        'sort_order'
    ];

    public function category()
    {
        return $this->belongsTo(ListingCategory::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }

    // Helper method to generate page title
    public function generateTitle()
    {
        $parts = [];
        
        if ($this->category) {
            $parts[] = $this->category->name;
        }
        
        if ($this->neighborhood) {
            $parts[] = $this->neighborhood->name;
        } elseif ($this->city) {
            $parts[] = $this->city->name;
        } elseif ($this->state) {
            $parts[] = $this->state->name;
        }

        return implode(' ', $parts);
    }
}
