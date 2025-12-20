<?php

namespace App\Services;

use App\Models\Facility;
use App\Models\Asset;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardService
{
    /**
     * Get the Schedule view for a specific date
     */
    /**
     * Get the Schedule view for a specific date
     */
    public function getDailySchedule(string $date)
    {
        // Parse the start and end of the selected day
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay   = Carbon::parse($date)->endOfDay();

        // Eager load bookings that OVERLAP with this day
        // Overlap Rule: (Start < DayEnd) AND (End > DayStart)
        return Facility::with(['bookings' => function ($query) use ($startOfDay, $endOfDay) {
            $query->where('start_time', '<', $endOfDay)
                  ->where('end_time', '>', $startOfDay)
                  ->whereIn('status', ['pending', 'approved'])
                  ->with('user'); // Also load user name for the schedule view
        }])->get();
    }

    /**
     * Get Damaged Assets for the alert list
     */
    public function getDamagedAssets()
    {
        return Asset::with('facility')
            ->whereIn('condition', ['Maintenance', 'Damaged'])
            ->paginate(10);
    }

    /**
     * Get Raw Data for the "Most Popular" chart
     */
    public function getPopularFacilitiesData()
    {
        return Booking::select('facility_id', DB::raw('count(*) as total'))
            ->groupBy('facility_id')
            ->orderByDesc('total')
            ->take(5)
            ->with('facility') // Eager load facility name
            ->get();
    }

    /**
     * Get Recent Audit Logs for Dashboard Widget
     */
    public function getRecentAuditLogs()
    {
        return \App\Models\AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }
}