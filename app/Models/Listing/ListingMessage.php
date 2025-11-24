<?php

namespace App\Models\Listing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ListingMessage extends Model
{
    use HasFactory;
    protected $fillable = [
        'listing_id',
        'user_id',
        'name',
        'email',
        'phone',
        'message'
    ];
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
}
