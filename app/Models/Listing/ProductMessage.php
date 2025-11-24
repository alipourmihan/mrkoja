<?php

namespace App\Models\Listing;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMessage extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'user_id',
        'name',
        'email',
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
