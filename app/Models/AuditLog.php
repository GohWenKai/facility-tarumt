<?php

/**
 * Audit Log Model
 * Author: [Your Name Here]
 * 
 * Purpose: Track all changes to critical models for accountability and security
 * Design Pattern: Observer Pattern (used in conjunction with AssetObserver)
 * 
 * Tracks: User actions, model changes, IP addresses, timestamps
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Relationship: Audit log belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
