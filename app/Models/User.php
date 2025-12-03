<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Requirement 1.1 (API Auth)

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * 
     * WE MODIFIED THIS: Added role, credits, and security fields 
     * so User::create() and update() works for these columns.
     */
    protected $fillable = [
        'name',
        'tarumt_id',
        'email',
        'password',
        'role',                   // 'admin', 'student', etc.
        'credits',                // Currency for booking
        'ip_address',             // Security tracking
        'last_login_at',          // Security tracking
        'failed_login_attempts',  // Rate limiting logic
        'address',
        'tel',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'ip_address', // Privacy: Don't send IP in API responses
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime', // Auto-convert to Carbon object
            'credits' => 'integer',
        ];
    }

    // --- RELATIONSHIPS (Day 1 Requirement) ---

    // A User has many Bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // A User (Admin) can have many Approvals (Day 2)
    public function approvals()
    {
        return $this->hasMany(BookingApproval::class, 'approver_id');
    }
}