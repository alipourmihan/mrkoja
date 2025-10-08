<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'name_en',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'latitude',
        'longitude',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($province) {
            if (empty($province->slug)) {
                $province->slug = Str::slug($province->name);
            }
        });

        static::updating(function ($province) {
            if ($province->isDirty('name') && empty($province->slug)) {
                $province->slug = Str::slug($province->name);
            }
        });
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getRouteKeyName()
    {
        return 'id';
    }
}