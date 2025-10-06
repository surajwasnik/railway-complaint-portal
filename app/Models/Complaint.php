<?php
// app/Models/Complaint.php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;
    protected $table = 'complaints';

    // Primary key
    protected $primaryKey = 'id';

    // The primary key is auto-incrementing
    public $incrementing = true;

    // The primary key is an integer
    protected $keyType = 'int';

    // If you want Laravel to automatically handle created_at and updated_at
    public $timestamps = true;
    protected $fillable = [
        'id',
        'station_id',
        'fir_number',
        'complainant_name',
        'complainant_number',
        'fir_description',
        'user_description',
        'police_station_name',
        'officer_name',
        'police_station_number',
        'status',
        'fir_date',
        'language'
    ];

    protected $casts = [
        'fir_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships

    /**
     * Complaint belongs to a User (complainant)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Complaint belongs to a Station
     */

    public function station()
    {
        return $this->belongsTo(\App\Models\Station::class);
    }

    /**
     * Get complainant user
     */
    public function complainant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByStation($query, $stationId)
    {
        return $query->where('station_id', $stationId);
    }
    public function stationAdmin()
    {
        return $this->belongsTo(User::class, 'user_id')->where('role_id', 2);
    }

    // Explicitly define the table name (not plural "complaints")

}
