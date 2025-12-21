<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HolidayService;
use Illuminate\Http\JsonResponse;

class HolidayController extends Controller
{
    protected HolidayService $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    /**
     * Get all holidays for a specific year
     */
    public function index(int $year): JsonResponse
    {
        // Validate year range (reasonable bounds)
        if ($year < 2020 || $year > 2030) {
            return response()->json([
                'error' => 'Year must be between 2020 and 2030'
            ], 400);
        }

        $holidays = $this->holidayService->getHolidaysForYear($year);

        return response()->json([
            'year' => $year,
            'country' => 'MY',
            'holidays' => $holidays
        ]);
    }

    /**
     * Check if a specific date is a holiday
     */
    public function check(string $date): JsonResponse
    {
        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return response()->json([
                'error' => 'Invalid date format. Use YYYY-MM-DD'
            ], 400);
        }

        $isHoliday = $this->holidayService->isHoliday($date);
        $holidayName = $isHoliday ? $this->holidayService->getHolidayName($date) : null;

        return response()->json([
            'date' => $date,
            'is_holiday' => $isHoliday,
            'name' => $holidayName
        ]);
    }
}
