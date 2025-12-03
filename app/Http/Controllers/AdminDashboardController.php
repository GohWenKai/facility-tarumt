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

        // 3. USE ADAPTER: Format Data for Frontend
        // We convert the Database Collection into a specific Chart array format
        $chartPayload = $this->chartAdapter->adaptPopularFacilities($rawChartData);

        // 4. Return View
        return view('admin.dashboard', [
            'facilities'   => $facilities,
            'selectedDate' => $selectedDate,
            'assets'       => $assets,
            'chartLabels'  => $chartPayload['labels'],
            'chartData'    => $chartPayload['data']
        ]);
    }
}