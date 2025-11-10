<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'imageable_id',
        'imageable_type',
        'filename',
        'original_name',
        'path',
        'mime_type',
        'size',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'size' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the parent imageable model (business or category).
     */
    public function imageable()
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include primary images.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    /**
     * Get the full URL of the image.
     */
    public function getUrlAttribute()
    {
        return 'http://localhost:8000/storage/' . $this->path;
    }
}