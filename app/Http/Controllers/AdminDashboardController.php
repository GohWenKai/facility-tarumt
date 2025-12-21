<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\AdminDashboardService;
use App\Adapters\ChartJsAdapter;

class AdminDashboardController extends Controller
{
    protected $dashboardService;
    protected $chartAdapter;

    // Dependency Injection (Service + Adapter)
    public function __construct(
        AdminDashboardService $dashboardService, 
        ChartJsAdapter $chartAdapter
    ) {
        $this->dashboardService = $dashboardService;
        $this->chartAdapter = $chartAdapter;
    }

    public function show(Request $request)
    {
        // 1. Determine Date
        $selectedDate = $request->input('date') 
            ? Carbon::parse($request->date)->format('Y-m-d') 
            : Carbon::today()->format('Y-m-d');

        // 2. USE SERVICE: Get Logic Data
        $facilities = $this->dashboardService->getDailySchedule($selectedDate);
        $assets = $this->dashboardService->getDamagedAssets();
        $rawChartData = $this->dashboardService->getPopularFacilitiesData();
        $recentLogs = $this->dashboardService->getRecentAuditLogs();

        // 3. NEW: Get Analytics Data
        $statistics = $this->dashboardService->getStatistics();
        $weeklyTrends = $this->dashboardService->getWeeklyTrends();
        $statusDistribution = $this->dashboardService->getStatusDistribution();
        $userRoles = $this->dashboardService->getUserRoleDistribution();
        $monthlyComparison = $this->dashboardService->getMonthlyComparison();
        $peakHours = $this->dashboardService->getPeakHours();

        // 4. USE ADAPTER: Format Data for Frontend
        $chartPayload = $this->chartAdapter->adaptPopularFacilities($rawChartData);

        // 5. Return View with ALL Data
        return view('admin.dashboard', [
            'facilities'   => $facilities,
            'selectedDate' => $selectedDate,
            'assets'       => $assets,
            'chartLabels'  => $chartPayload['labels'],
            'chartData'    => $chartPayload['data'],
            'recentLogs'   => $recentLogs,
            // NEW Analytics Data
            'statistics'   => $statistics,
            'weeklyTrends' => $weeklyTrends,
            'statusDistribution' => $statusDistribution,
            'userRoles'    => $userRoles,
            'monthlyComparison' => $monthlyComparison,
            'peakHours'    => $peakHours,
        ]);
    }
}