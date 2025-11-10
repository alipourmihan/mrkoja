<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'province_id',
        'city_id',
        'neighborhood_id',
        'title',
        'meta_description',
        'content',
        'h1',
        'og_image',
        'custom_text',
        'keywords',
        'og_title',
        'og_description',
        'twitter_title',
        'twitter_description',
        'canonical_url',
        'schema_markup',
        'h2',
        'h3',
        'faq_data',
        'is_indexed',
        'is_followed',
        'priority',
        'changefreq',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class);
    }

    protected $casts = [
        'schema_markup' => 'array',
        'faq_data' => 'array',
        'is_indexed' => 'boolean',
        'is_followed' => 'boolean',
        'priority' => 'decimal:1',
    ];
}


