<?php

namespace App\Models;

use App\Models\Instrument\Equipment;
use App\Models\Instrument\EquipmentReview;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use App\Models\Listing\Listing;
use App\Models\BaseModel;

class Vendor extends BaseModel implements AuthenticatableContract
{
    use HasFactory, Authenticatable;

    protected $fillable = [
        'photo',
        'email',
        'to_mail',
        'phone',
        'username',
        'password',
        'status',
        'amount',
        'facebook',
        'twitter',
        'linkedin',
        'avg_rating',
        'email_verified_at',
        'show_email_addresss',
        'show_phone_number',
        'show_contact_form',
    ];
    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'email_verified_at' => 'timestamp',
    ];
    public function vendor_infos()
    {
        return $this->hasMany(VendorInfo::class);
    }
    public function vendor_info()
    {
        return $this->hasOne(VendorInfo::class);
    }

    //support ticket
    public function support_ticket()
    {
        return $this->hasMany(SupportTicket::class, 'user_id', 'id');
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class, 'user_id', 'id');
    }
    public function messages()
    {
        return $this->hasMany(Listing::class, 'user_id', 'id');
    }
}
