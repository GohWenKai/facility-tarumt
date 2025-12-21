<?php

namespace App\Services;

use App\Models\Facility;
use App\Models\Asset;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardService
{
    /**
     * Get the Schedule view for a specific date
     */
    public function getDailySchedule(string $date)
    {
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay   = Carbon::parse($date)->endOfDay();

        return Facility::with(['bookings' => function ($query) use ($startOfDay, $endOfDay) {
            $query->where('start_time', '<', $endOfDay)
                  ->where('end_time', '>', $startOfDay)
                  ->whereIn('status', ['pending', 'approved'])
                  ->with('user');
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
            ->with('facility')
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

    // ==================== NEW ANALYTICS METHODS ====================

    /**
     * Get Key Statistics for Dashboard Cards
     */
    public function getStatistics()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_bookings' => Booking::count(),
            'today_bookings' => Booking::whereDate('start_time', $today)->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'monthly_bookings' => Booking::where('created_at', '>=', $thisMonth)->count(),
            'total_users' => User::whereIn('role', ['student', 'lecturer'])->count(),
            'total_facilities' => Facility::count(),
            'total_assets' => Asset::count(),
            'damaged_assets' => Asset::whereIn('condition', ['Maintenance', 'Damaged'])->count(),
        ];
    }

    /**
     * Get Weekly Booking Trends (Last 7 Days)
     */
    public function getWeeklyTrends()
    {
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('D'); // Mon, Tue, etc.
            $data[] = Booking::whereDate('created_at', $date)->count();
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Get Booking Status Distribution (Pie Chart)
     */
    public function getStatusDistribution()
    {
        $statuses = Booking::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'labels' => array_map('ucfirst', array_keys($statuses)),
            'data' => array_values($statuses),
        ];
    }

    /**
     * Get User Role Distribution (Students vs Lecturers)
     */
    public function getUserRoleDistribution()
    {
        $roles = User::select('role', DB::raw('count(*) as total'))
            ->whereIn('role', ['student', 'lecturer'])
            ->groupBy('role')
            ->pluck('total', 'role')
            ->toArray();

        return [
            'labels' => array_map('ucfirst', array_keys($roles)),
            'data' => array_values($roles),
        ];
    }

    /**
     * Get Monthly Booking Comparison (This Month vs Last Month)
     */
    public function getMonthlyComparison()
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        return [
            'this_month' => Booking::where('created_at', '>=', $thisMonth)->count(),
            'last_month' => Booking::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count(),
        ];
    }

    /**
     * Get Peak Hours (Most bookings by hour)
     */
    public function getPeakHours()
    {
        $hours = Booking::select(DB::raw('HOUR(start_time) as hour'), DB::raw('count(*) as total'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $labels = [];
        $data = array_fill(0, 24, 0); // Initialize 24 hours with 0

        foreach ($hours as $h) {
            $data[$h->hour] = $h->total;
        }

        // Only show business hours (8am - 10pm)
        $filteredLabels = [];
        $filteredData = [];
        for ($i = 8; $i <= 22; $i++) {
            $filteredLabels[] = sprintf('%02d:00', $i);
            $filteredData[] = $data[$i];
        }

        return ['labels' => $filteredLabels, 'data' => $filteredData];
    }
}