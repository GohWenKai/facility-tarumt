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
    public function getDailySchedule(string $date)
    {
        // Eager load bookings ONLY for the selected date
        return Facility::with(['bookings' => function ($query) use ($date) {
            $query->whereDate('start_time', $date)
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
}