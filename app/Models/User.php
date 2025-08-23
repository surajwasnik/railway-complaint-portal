<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends \TCG\Voyager\Models\User
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

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
    ];
    public function station()
{
    return $this->hasOne(Station::class);
}

/**
 * User can have many complaints (if they are complainants)
 */
public function complaints()
{
    return $this->hasMany(Complaint::class);
}

/**
 * Check if user is super admin
 */
public function isSuperAdmin()
{
    return $this->role_id == 1; // Assuming role_id 1 is super admin
}

/**
 * Check if user is station admin
 */
public function isStationAdmin()
{
    return $this->role_id == 2; // Assuming role_id 2 is station admin
}

/**
 * Get user's station (if station admin)
 */
public function managedStation()
{
    return $this->hasOne(Station::class, 'user_id');
}
public function scopeStationAdmins($query)
{
    return $query->where('role_id', 2);
}

// Or if you're using Voyager's roles:
// public function scopeStationAdmins($query)
// {
//     return $query->whereHas('role', function($q) {
//         $q->where('name', 'station_admin');
//     });
// }
public static function getStationAdmins()
{
    return self::where('role_id', 2)->pluck('name', 'id');
}
}
