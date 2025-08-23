<?php
// app/Models/Station.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'station_name',
        'station_code',
        'station_head_name',
        'station_head_phone',
        'city',
        'state',
        'address',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships

    /**
     * Station belongs to a User (Station Admin)
     */
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    /**
     * Station has many complaints
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Get the station admin (user who manages this station)
     */
    // public function stationAdmin()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }
    public function user()
{
    return $this->belongsTo(User::class)->where('role_id', 2);
}

// Add a separate relationship for station admins specifically
public function stationAdmin()
{
    return $this->belongsTo(User::class, 'user_id')->where('role_id', 2);
}
}
