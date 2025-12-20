<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'name',
        'type',
        'capacity',
        'status',
        'start_time',
        'end_time',
        'image_path'
    ];

// app/Models/Facility.php

protected $casts = [
    // This creates a Carbon instance automatically when you access $facility->start_time
    'start_time' => 'datetime:H:i', 
    'end_time'   => 'datetime:H:i',
];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}