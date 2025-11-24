<?php

namespace App\Models;

use App\Models\Instrument\EquipmentBooking;
use App\Models\Instrument\EquipmentReview;
use App\Models\Listing\ListingReview;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
  use HasFactory, Notifiable;

  /**
   * The attributes that aren't mass assignable.
   *
   * @var array
   */
  protected $guarded = [];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'amount' => 'decimal:2',
    'avg_rating' => 'decimal:2',
    'status' => 'integer',
    'show_email_addresss' => 'boolean',
    'show_phone_number' => 'boolean',
    'show_contact_form' => 'boolean',
  ];

  /**
   * Scope a query to only include users with role 'user'.
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeUsers($query)
  {
    return $query->where('role', 'user');
  }

  /**
   * Scope a query to only include users with role 'business'.
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeBusinesses($query)
  {
    return $query->where('role', 'business');
  }

  /**
   * Scope a query to only include active users.
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeActive($query)
  {
    return $query->where('status', 1);
  }

  /**
   * Check if user has role 'user'.
   *
   * @return bool
   */
  public function isUser(): bool
  {
    return $this->role === 'user';
  }

  /**
   * Check if user has role 'business'.
   *
   * @return bool
   */
  public function isBusiness(): bool
  {
    return $this->role === 'business';
  }

  /**
   * Get vendor info relationship (for business users).
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasOne
   */
  public function vendorInfo()
  {
    return $this->hasOne(VendorInfo::class, 'user_id', 'id');
  }

  /**
   * Get vendor infos relationship (for business users).
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function vendorInfos()
  {
    return $this->hasMany(VendorInfo::class, 'user_id', 'id');
  }

  /**
   * Get listings relationship (for business users).
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function listings()
  {
    return $this->hasMany(Listing\Listing::class, 'user_id', 'id');
  }

  /**
   * Get memberships relationship (for business users).
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function memberships()
  {
    return $this->hasMany(Membership::class, 'user_id', 'id');
  }

  /**
   * Get support tickets relationship.
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function supportTickets()
  {
    return $this->hasMany(SupportTicket::class, 'user_id', 'id');
  }

  /**
   * Get listing reviews relationship.
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function listingReview()
  {
    return $this->hasMany(ListingReview::class, 'user_id', 'id');
  }

  /**
   * Get equipment bookings relationship.
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function equipmentBooking()
  {
    return $this->hasMany(EquipmentBooking::class);
  }

  /**
   * Get equipment reviews relationship.
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function equipmentReview()
  {
    return $this->hasMany(EquipmentReview::class, 'user_id', 'id');
  }
}
