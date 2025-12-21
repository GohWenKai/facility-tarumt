<?php

namespace App\Services;

use App\Models\PublicHoliday;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HolidayService
{
    /**
     * Nager.Date API Base URL
     * Free, no API key required, no rate limits
     */
    private const API_BASE_URL = 'https://date.nager.at/api/v3';
    
    /**
     * Use Singapore as base (shares many holidays with Malaysia)
     * Malaysia (MY) is not supported by Nager.Date API
     */
    private const API_COUNTRY_CODE = 'SG';
    
    /**
     * Country code for stored holidays
     */
    private const COUNTRY_CODE = 'MY';

    /**
     * Malaysian-specific holidays not covered by Singapore API
     * These are national holidays unique to Malaysia
     */
    private function getMalaysianSpecificHolidays(int $year): array
    {
        $holidays = [];
        
        // Fixed holidays that occur on the same date each year
        $fixedHolidays = [
            ['date' => "{$year}-02-01", 'name' => 'Federal Territory Day'],
            ['date' => "{$year}-08-31", 'name' => 'National Day (Merdeka)'],
            ['date' => "{$year}-09-16", 'name' => 'Malaysia Day'],
        ];
        
        // Variable holidays (Islamic calendar based - these are approximations)
        // In production, you might want to use a more accurate Islamic calendar API
        $variableHolidays = match($year) {
            2024 => [
                ['date' => '2024-01-25', 'name' => 'Thaipusam'],
                ['date' => '2024-03-28', 'name' => 'Nuzul Al-Quran'],
                ['date' => '2024-04-10', 'name' => 'Hari Raya Aidilfitri'],
                ['date' => '2024-04-11', 'name' => 'Hari Raya Aidilfitri (2nd Day)'],
                ['date' => '2024-06-17', 'name' => 'Hari Raya Haji'],
                ['date' => '2024-07-07', 'name' => 'Awal Muharram'],
                ['date' => '2024-09-16', 'name' => 'Prophet Muhammad\'s Birthday'],
                ['date' => '2024-10-31', 'name' => 'Deepavali'],
            ],
            2025 => [
                ['date' => '2025-01-14', 'name' => 'Thaipusam'],
                ['date' => '2025-03-17', 'name' => 'Nuzul Al-Quran'],
                ['date' => '2025-03-30', 'name' => 'Hari Raya Aidilfitri'],
                ['date' => '2025-03-31', 'name' => 'Hari Raya Aidilfitri (2nd Day)'],
                ['date' => '2025-06-06', 'name' => 'Hari Raya Haji'],
                ['date' => '2025-06-27', 'name' => 'Awal Muharram'],
                ['date' => '2025-09-05', 'name' => 'Prophet Muhammad\'s Birthday'],
                ['date' => '2025-10-20', 'name' => 'Deepavali'],
            ],
            2026 => [
                ['date' => '2026-02-01', 'name' => 'Thaipusam'],
                ['date' => '2026-03-06', 'name' => 'Nuzul Al-Quran'],
                ['date' => '2026-03-20', 'name' => 'Hari Raya Aidilfitri'],
                ['date' => '2026-03-21', 'name' => 'Hari Raya Aidilfitri (2nd Day)'],
                ['date' => '2026-05-27', 'name' => 'Hari Raya Haji'],
                ['date' => '2026-06-16', 'name' => 'Awal Muharram'],
                ['date' => '2026-08-25', 'name' => 'Prophet Muhammad\'s Birthday'],
                ['date' => '2026-11-08', 'name' => 'Deepavali'],
            ],
            default => [],
        };
        
        return array_merge($fixedHolidays, $variableHolidays);
    }

    /**
     * Fetch holidays from Nager.Date API (using Singapore as base)
     */
    public function fetchFromApi(int $year): array
    {
        try {
            $response = Http::timeout(30)->get(self::API_BASE_URL . '/PublicHolidays/' . $year . '/' . self::API_COUNTRY_CODE);
            
            if ($response->successful()) {
                $data = $response->json();
                return is_array($data) ? $data : [];
            }
            
            Log::warning("Holiday API returned non-success status", [
                'year' => $year,
                'status' => $response->status()
            ]);
            
            return [];
        } catch (\Exception $e) {
            Log::error("Failed to fetch holidays from API", [
                'year' => $year,
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }

    /**
     * Sync holidays from API to database
     */
    public function syncHolidays(int $year): int
    {
        $count = 0;
        
        // First, add Malaysian-specific holidays (these take priority)
        $malaysianHolidays = $this->getMalaysianSpecificHolidays($year);
        foreach ($malaysianHolidays as $holiday) {
            PublicHoliday::updateOrCreate(
                [
                    'date' => $holiday['date'],
                    'country_code' => self::COUNTRY_CODE,
                ],
                [
                    'name' => $holiday['name'],
                    'local_name' => $holiday['name'],
                    'year' => $year,
                    'is_fixed' => true,
                ]
            );
            $count++;
        }
        
        // Then fetch Singapore holidays from API (shared holidays like CNY, Christmas, etc.)
        $apiHolidays = $this->fetchFromApi($year);
        
        if (!empty($apiHolidays)) {
            foreach ($apiHolidays as $holiday) {
                // Skip if we already have this date from Malaysian-specific holidays
                $exists = PublicHoliday::where('date', $holiday['date'])
                    ->where('country_code', self::COUNTRY_CODE)
                    ->exists();
                    
                if (!$exists) {
                    PublicHoliday::updateOrCreate(
                        [
                            'date' => $holiday['date'],
                            'country_code' => self::COUNTRY_CODE,
                        ],
                        [
                            'name' => $holiday['name'],
                            'local_name' => $holiday['localName'] ?? $holiday['name'],
                            'year' => $year,
                            'is_fixed' => $holiday['fixed'] ?? false,
                        ]
                    );
                    $count++;
                }
            }
        }
        
        Log::info("Synced {$count} holidays for year {$year}");
        
        return $count;
    }

    /**
     * Check if a date is a public holiday
     */
    public function isHoliday(string $date): bool
    {
        // First check database
        if (PublicHoliday::isHoliday($date)) {
            return true;
        }
        
        // If no holidays found for this year, try to sync
        $year = Carbon::parse($date)->year;
        if (!PublicHoliday::forYear($year)->exists()) {
            $this->syncHolidays($year);
            return PublicHoliday::isHoliday($date);
        }
        
        return false;
    }

    /**
     * Get holiday name for a specific date
     */
    public function getHolidayName(string $date): ?string
    {
        $holidayName = PublicHoliday::getHolidayName($date);
        
        // If no holidays found for this year, try to sync
        if ($holidayName === null) {
            $year = Carbon::parse($date)->year;
            if (!PublicHoliday::forYear($year)->exists()) {
                $this->syncHolidays($year);
                return PublicHoliday::getHolidayName($date);
            }
        }
        
        return $holidayName;
    }

    /**
     * Get all holidays for a specific year
     */
    public function getHolidaysForYear(int $year): array
    {
        // Check if we have holidays for this year
        if (!PublicHoliday::forYear($year)->exists()) {
            $this->syncHolidays($year);
        }
        
        return PublicHoliday::forYear($year)
            ->orderBy('date')
            ->get()
            ->map(function ($holiday) {
                return [
                    'date' => $holiday->date->format('Y-m-d'),
                    'name' => $holiday->name,
                    'local_name' => $holiday->local_name,
                ];
            })
            ->toArray();
    }
}
