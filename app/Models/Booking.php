<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    // 1. Configure for UUID (Since your SQL uses CHAR(36) for ID)
    public $incrementing = false; // ID is not an auto-incrementing number
    protected $keyType = 'string'; // ID is a string (UUID)

    // 2. Allow Mass Assignment
    protected $fillable = [
        'id',           // We must fill this manually or generate it
        'user_id',
        'facility_id',
        'start_time',
        'end_time',
        'total_cost',
        'status',       // 'pending', 'approved', 'rejected'
    ];

    // 3. Auto-convert Dates
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_cost' => 'integer',
    ];

    // --- RELATIONSHIPS ---

    // A Booking belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A Booking belongs to a Facility
    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    // A Booking has one Approval record (For Module 7 - Day 2)
    public function approval()
    {
        return $this->hasOne(BookingApproval::class);
    }
}