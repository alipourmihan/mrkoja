<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Listing\ListingContent;

class ListingCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'language_id',
        'parent_id',
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'icon'
    ];
    
    public function listing_contents()
    {
        return $this->hasMany(ListingContent::class, 'category_id');
    }
    
    public function parent()
    {
        return $this->belongsTo(ListingCategory::class, 'parent_id');
    }
    
    public function children()
    {
        return $this->hasMany(ListingCategory::class, 'parent_id');
    }
}
