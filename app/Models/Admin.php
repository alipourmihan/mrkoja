<?php

namespace App\Models;

use App\Models\RolePermission;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
class Admin extends BaseModel implements AuthenticatableContract
{
  use HasFactory, Authenticatable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'role_id',
    'first_name',
    'last_name',
    'image',
    'username',
    'email',
    'show_email_address',
    'phone',
    'show_phone_number',
    'password',
    'status',
    'address',
    'details'
  ];
  protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime'
];
  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = ['password'];

  public function role()
  {
    return $this->belongsTo(RolePermission::class, 'role_id', 'id');
  }
}
