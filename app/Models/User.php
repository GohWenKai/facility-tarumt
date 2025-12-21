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
        'locked_until',           // Time-based account unlock
        'address',
        'tel',
        // 2FA Fields
        'two_factor_enabled',
        'two_factor_method',      // 'email' or 'sms'
        'two_factor_code',
        'two_factor_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'ip_address', // Privacy: Don't send IP in API responses
        'two_factor_code', // Never expose OTP code
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
            'locked_until' => 'datetime',   // Time-based account unlock
            'credits' => 'integer',
            'two_factor_enabled' => 'boolean',
            'two_factor_expires_at' => 'datetime',
        ];
    }

    // --- 2FA HELPER METHODS ---

    /**
     * Generate and store a new OTP code
     */
    public function generateTwoFactorCode(): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $this->two_factor_code = $code;
        $this->two_factor_expires_at = now()->addMinutes(5);
        $this->save();
        
        return $code;
    }

    /**
     * Verify if a code is valid
     */
    public function verifyTwoFactorCode(string $code): bool
    {
        if (!$this->two_factor_code || !$this->two_factor_expires_at) {
            return false;
        }

        if (now()->isAfter($this->two_factor_expires_at)) {
            return false; // Code expired
        }

        return $this->two_factor_code === $code;
    }

    /**
     * Clear the 2FA code after successful verification
     */
    public function clearTwoFactorCode(): void
    {
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
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

    // Relationship: User has many credit transactions
    public function transactions()
    {
        return $this->hasMany(CreditTransaction::class)->orderBy('created_at', 'desc');
    }
}