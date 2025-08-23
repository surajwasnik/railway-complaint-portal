<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    // Explicitly define the table name (not plural "complaints")
    protected $table = 'complaints';

    // Primary key
    protected $primaryKey = 'id';

    // The primary key is auto-incrementing
    public $incrementing = true;

    // The primary key is an integer
    protected $keyType = 'int';

    // If you want Laravel to automatically handle created_at and updated_at
    public $timestamps = true;

    // Allow mass assignment for these columns
    protected $fillable = [
        'fir_number',
        'description',
    ];
}
