<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicHoliday extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'name',
        'local_name',
        'country_code',
        'year',
        'is_fixed',
    ];

    protected $casts = [
        'date' => 'date',
        'is_fixed' => 'boolean',
    ];

    /**
     * Scope to filter by year
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope to filter by date
     */
    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Check if a specific date is a public holiday
     */
    public static function isHoliday(string $date): bool
    {
        return self::whereDate('date', $date)->exists();
    }

    /**
     * Get holiday name for a specific date
     */
    public static function getHolidayName(string $date): ?string
    {
        $holiday = self::whereDate('date', $date)->first();
        return $holiday?->name;
    }
}
