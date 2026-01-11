<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'university',
        'type',
        'image',
        'phone',
        'password',
        'google_id',
        'role',
        'admin_access',
        'permissions',
        'housing_context',
        'profile_complete',
        'preferred_contact',
        'whatsapp_enabled',
    ];

  //public function likes()
  //{
   //return $this->belongsToMany(Like::class,'like')->withTimestamps();
  //}

  public function likes()
{
    return $this->hasMany(Like::class);
}

public function sentRoomShareRequests()
{
    return $this->hasMany(RoomShareRequest::class, 'sender_id');
}

public function receivedRoomShareRequests()
{
    return $this->hasMany(RoomShareRequest::class, 'receiver_id');
}

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array',
        'profile_complete' => 'boolean',
        'whatsapp_enabled' => 'boolean',
    ];

    /**
     * Check if user has a specific permission
     */
    public function hasPermission($permission)
    {
        if ($this->role === 'admin') {
            return true; // Admin has all permissions
        }

        if (!$this->admin_access) {
            return false; // No admin access
        }

        return in_array($permission, $this->permissions ?? []);
    }
}
