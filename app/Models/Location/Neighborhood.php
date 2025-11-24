<?php

namespace App\Models\Location;

use App\Models\Listing\ListingContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'state_id',
        'city_id',
        'name',
        'slug'
    ];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function listing_neighborhood()
    {
        return $this->hasMany(ListingContent::class, 'neighborhood_id');
    }
}
