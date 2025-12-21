@extends('layouts.admin')

@section('content')

{{-- ================================================================================= --}}
{{-- 1. EMBEDDED CSS (Modern Dashboard Styles)                                         --}}
{{-- ================================================================================= --}}
<style>
    /* GLOBAL DASHBOARD UTILS */
    .dashboard-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    .dashboard-header {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem;
    }

    /* SCHEDULE TABLE STYLES */
    .schedule-scroll-area {
        max-height: 70vh;
        overflow: auto;
    }
    .schedule-scroll-area::-webkit-scrollbar { width: 8px; height: 8px; }
    .schedule-scroll-area::-webkit-scrollbar-track { background: #f8fafc; }
    .schedule-scroll-area::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .modern-table th, .modern-table td {
        border-right: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
        height: 50px;
        min-width: 60px;
        padding: 0;
        text-align: center;
        vertical-align: middle;
        position: relative;
    }

    /* Sticky Headers */
    .modern-table thead th {
        position: sticky;
        top: 0;
        z-index: 20;
        background: #1e293b;
        color: #fff;
        font-weight: 500;
        font-size: 0.75rem;
        border: none;
    }
    .sticky-date-col {
        position: sticky;
        left: 0;
        z-index: 10;
        background: #ffffff;
        width: 90px;
        border-right: 2px solid #e2e8f0;
    }
    .modern-table thead th.sticky-date-col {
        z-index: 30;
        background: #0f172a;
    }

    /* Status Colors */
    .slot-approved { background-color: #fee2e2; color: #ef4444; }
    .slot-approved:hover { background-color: #fca5a5; cursor: pointer; }
    
    .slot-pending { 
        background-color: #fef3c7; color: #d97706; 
        background-image: repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(255,255,255,0.5) 5px, rgba(255,255,255,0.5) 10px);
    }
    
    .slot-past { 
        background-color: #f8fafc; 
        background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
        background-size: 6px 6px; 
    }
    .slot-free { background-color: #fff; }
    .slot-free:hover { background-color: #f1f5f9; }

    /* Highlights */
    .is-today .sticky-date-col { background: #eff6ff; color: #2563eb; border-right: 2px solid #2563eb; }

    /* ==================== PREMIUM STAT CARDS ==================== */
    .stat-card {
        border-radius: 16px;
        padding: 1.5rem;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.25);
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 60%);
        pointer-events: none;
    }
    .stat-icon {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 3rem;
        opacity: 0.2;
    }
    .stat-content {
        position: relative;
        z-index: 1;
    }
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        line-height: 1;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    .stat-label {
        margin: 0.5rem 0 0 0;
        font-size: 0.9rem;
        opacity: 0.9;
        font-weight: 500;
    }
    .stat-trend {
        margin-top: 1rem;
        font-size: 0.8rem;
        opacity: 0.85;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .stat-trend i {
        font-size: 0.9rem;
    }
</style>

<div class="container-fluid px-4 py-4" style="background-color: #f8fafc; min-height: 100vh;">

{{-- ================================================================================= --}}
{{-- SECTION 0: STATISTICS CARDS (Premium Design)                                       --}}
{{-- ================================================================================= --}}
<div class="row g-4 mb-4">
    <!-- Total Bookings -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="stat-icon">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div class="stat-content">
                <h2 class="stat-number">{{ $statistics['total_bookings'] }}</h2>
                <p class="stat-label">Total Bookings</p>
            </div>
            <div class="stat-trend">
                <i class="bi bi-arrow-up-right"></i>
                <span>+{{ $statistics['monthly_bookings'] }} this month</span>
            </div>
        </div>
    </div>

    <!-- Today's Bookings -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="stat-icon">
                <i class="bi bi-calendar-day"></i>
            </div>
            <div class="stat-content">
                <h2 class="stat-number">{{ $statistics['today_bookings'] }}</h2>
                <p class="stat-label">Today's Bookings</p>
            </div>
            <div class="stat-trend">
                <i class="bi bi-clock"></i>
                <span>{{ now()->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Pending Approval -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="stat-icon">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="stat-content">
                <h2 class="stat-number">{{ $statistics['pending_bookings'] }}</h2>
                <p class="stat-label">Pending Approval</p>
            </div>
            <div class="stat-trend">
                <a href="{{ route('bookings.approval') }}" class="text-white text-decoration-none">
                    <i class="bi bi-arrow-right-circle"></i> View All
                </a>
            </div>
        </div>
    </div>

    <!-- Total Users -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="stat-icon">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-content">
                <h2 class="stat-number">{{ $statistics['total_users'] }}</h2>
                <p class="stat-label">Active Users</p>
            </div>
            <div class="stat-trend">
                <i class="bi bi-building"></i>
                <span>{{ $statistics['total_facilities'] }} Facilities</span>
            </div>
        </div>
    </div>
</div>

{{-- ================================================================================= --}}
{{-- SECTION 1: ANALYTICS CHARTS (Premium Multi-Chart Layout)                          --}}
{{-- ================================================================================= --}}
<div class="row g-4 mb-4">
    <!-- Weekly Trends (Line Chart) -->
    <div class="col-lg-8">
        <div class="dashboard-card h-100">
            <div class="dashboard-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark m-0">
                        <i class="bi bi-graph-up text-primary me-2"></i>Booking Trends
                    </h5>
                    <small class="text-muted">Last 7 days activity</small>
                </div>
                <div class="d-flex gap-2">
                    @php
                        $change = $monthlyComparison['this_month'] - $monthlyComparison['last_month'];
                        $changePercent = $monthlyComparison['last_month'] > 0 
                            ? round(($change / $monthlyComparison['last_month']) * 100) 
                            : 0;
                    @endphp
                    <span class="badge {{ $change >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-75 px-3 py-2">
                        <i class="bi bi-{{ $change >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                        {{ abs($changePercent) }}% vs last month
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="weeklyTrendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Distribution (Doughnut) -->
    <div class="col-lg-4">
        <div class="dashboard-card h-100">
            <div class="dashboard-header">
                <h5 class="fw-bold text-dark m-0">
                    <i class="bi bi-pie-chart text-warning me-2"></i>Booking Status
                </h5>
                <small class="text-muted">Distribution by status</small>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="height: 260px; width: 260px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Popular Facilities (Bar Chart) -->
    <div class="col-lg-6">
        <div class="dashboard-card h-100">
            <div class="dashboard-header">
                <h5 class="fw-bold text-dark m-0">
                    <i class="bi bi-bar-chart-fill text-primary me-2"></i>Popular Facilities
                </h5>
                <small class="text-muted">Top 5 most booked</small>
            </div>
            <div class="card-body">
                <div style="height: 280px;">
                    <canvas id="popularityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Peak Hours (Bar Chart) -->
    <div class="col-lg-6">
        <div class="dashboard-card h-100">
            <div class="dashboard-header">
                <h5 class="fw-bold text-dark m-0">
                    <i class="bi bi-clock-history text-success me-2"></i>Peak Hours
                </h5>
                <small class="text-muted">Busiest booking times</small>
            </div>
            <div class="card-body">
                <div style="height: 280px;">
                    <canvas id="peakHoursChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- User Distribution (Doughnut) -->
    <div class="col-lg-4">
        <div class="dashboard-card h-100">
            <div class="dashboard-header">
                <h5 class="fw-bold text-dark m-0">
                    <i class="bi bi-people-fill text-info me-2"></i>User Breakdown
                </h5>
                <small class="text-muted">Students vs Lecturers</small>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="height: 220px; width: 220px;">
                    <canvas id="userRolesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Security/Activity Feed -->
    <div class="col-lg-8">
        <div class="dashboard-card h-100">
            <div class="dashboard-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark m-0">
                        <i class="bi bi-shield-check text-success me-2"></i>Recent Activity
                    </h5>
                    <small class="text-muted">Live security feed</small>
                </div>
                <a href="{{ route('admin.audit_logs.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="list-group list-group-flush" style="max-height: 280px; overflow-y: auto;">
                @forelse($recentLogs as $log)
                    <div class="list-group-item px-3 py-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <small class="fw-bold text-dark">{{ $log->user->name ?? 'System' }}</small>
                            <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            @php
                                $icon = match($log->action) {
                                    'created' => 'bi-plus-circle text-success',
                                    'updated' => 'bi-pencil text-warning',
                                    'deleted' => 'bi-trash text-danger',
                                    'restored' => 'bi-arrow-counterclockwise text-primary',
                                    default => 'bi-activity text-secondary'
                                };
                            @endphp
                            <i class="bi {{ $icon }} me-2"></i>
                            <span class="text-muted small">
                                {{ ucfirst($log->action) }} 
                                <strong>{{ class_basename($log->model_type) }} #{{ $log->model_id }}</strong>
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-shield-check fs-4 d-block mb-2"></i>
                        No recent activity
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

    {{-- ================================================================================= --}}
    {{-- SECTION 2: BOOKING SCHEDULE (Main Tool)                                           --}}
    {{-- ================================================================================= --}}
    
@php
    // define time slots - Full 24-hour range to show all bookings (including midnight)
    $startHour = 0; 
    $endHour = 24; 
    $timeSlots = [];
    for ($h = $startHour; $h < $endHour; $h++) {
        $timeSlots[] = sprintf('%02d:00', $h);
        $timeSlots[] = sprintf('%02d:30', $h);
    }
@endphp

<div class="dashboard-card">
    <div class="dashboard-header">
        <div class="row align-items-center g-3">
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-2 rounded me-3 text-primary">
                        <i class="bi bi-grid-3x3-gap fs-4"></i>
                    </div>
                    <div>
                        <h5 class="m-0 fw-bold text-dark">Daily Facility View</h5>
                        <span class="text-muted small">{{ \Carbon\Carbon::parse($selectedDate)->format('l, d F Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <form action="{{ route('dashboard') }}" method="GET">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-event text-muted"></i></span>
                        <input type="date" name="date" class="form-control border-start-0" 
                               value="{{ $selectedDate }}" 
                               onchange="this.form.submit()">
                    </div>
                </form>
            </div>

            <div class="col-lg-4 col-md-12 text-lg-end">
                <!-- Legend -->
                <div class="d-inline-flex gap-3 align-items-center bg-light px-3 py-2 rounded-pill border">
                    
                    <!-- Booked -->
                    <div class="d-flex align-items-center small">
                        <span class="d-inline-block rounded-circle bg-danger opacity-50 me-2" 
                            style="width: 12px; height: 12px;"></span> 
                        Booked
                    </div>

                    <!-- Pending -->
                    <div class="d-flex align-items-center small">
                        <span class="d-inline-block rounded-circle bg-warning opacity-50 me-2" 
                            style="width: 12px; height: 12px;"></span> 
                        Pending
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="schedule-scroll-area">
        <table class="modern-table">
            <thead>
                <tr>
                    <!-- Left Sticky Column: Facility Name -->
                    <th class="sticky-date-col" style="width: 150px; min-width: 150px;">Facility</th>
                    
                    <!-- Top Sticky Header: Time Slots -->
                    @foreach($timeSlots as $slot)
                        <th>{{ $slot }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($facilities as $facility)
                    <tr>
                        <!-- Facility Name Row Header -->
                        <td class="sticky-date-col text-start ps-3 fw-bold text-dark" style="background-color: #f8fafc;">
                            {{ $facility->name }}
                        </td>

                        <!-- Time Slots Cells -->
                        @foreach($timeSlots as $slot)
                            @php
                                // Create a Carbon instance for this specific slot (Date + Time)
                                $currentSlotTime = \Carbon\Carbon::parse($selectedDate . ' ' . $slot);
                                $status = 'free';
                                
                                // Check the eager-loaded bookings for this facility
                                foreach ($facility->bookings as $bk) {
                                    $bkStart = \Carbon\Carbon::parse($bk->start_time);
                                    $bkEnd   = \Carbon\Carbon::parse($bk->end_time);

                                    // Check overlap
                                    if ($currentSlotTime->greaterThanOrEqualTo($bkStart) && $currentSlotTime->lessThan($bkEnd)) {
                                        $status = strtolower($bk->status);
                                        break; 
                                    }
                                }

                                // Mark past slots if needed (optional)
                                if ($status == 'free' && $currentSlotTime->lessThan(now())) {
                                    $status = 'past';
                                }
                            @endphp

                            @if ($status == 'approved')
                                <td class="slot-approved" title="Booked by User ID {{ $bk->user_id ?? '' }}">
                                    <i class="bi bi-check-lg small"></i>
                                </td>
                            @elseif ($status == 'pending')
                                <td class="slot-pending" title="Pending Approval">
                                    <i class="bi bi-hourglass-split small"></i>
                                </td>
                            @elseif ($status == 'past')
                                <td class="slot-past"></td>
                            @else
                                <td class="slot-free"></td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

    {{-- ================================================================================= --}}
    {{-- SECTION 3: MAINTENANCE ALERTS (Asset Table)                                       --}}
    {{-- ================================================================================= --}}
    <div class="dashboard-card border-warning border-opacity-25">
        <div class="dashboard-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark m-0">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Maintenance Queue
                </h5>
                <small class="text-muted">Assets marked as damaged or under maintenance</small>
            </div>
            
            <a href="{{ route('admin.assets.report') }}" class="btn btn-sm btn-dark" target="_blank">
                <i class="bi bi-file-earmark-pdf me-1"></i> Generate Report
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Facility</th>
                        <th>Asset Name</th>
                        <th>Type</th>
                        <th>Condition</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr>
                        <td class="ps-4 fw-bold text-muted">#{{ $asset->id }}</td>
                        
                        <!-- Facility -->
                        <td class="text-muted">
                            {{ $asset->facility ? $asset->facility->name : 'Unassigned' }}
                        </td>
                        
                        <!-- Asset Name -->
                        <td class="fw-bold">{{ $asset->name }}</td>
                        
                        <!-- Type & Serial -->
                        <td>
                            {{ $asset->type }} <br>
                            <span class="text-muted small">{{ $asset->serial_number }}</span>
                        </td>
                        
                        <!-- Condition Badge -->
                        <td>
                            @php
                                $badgeClass = match($asset->condition) {
                                    'Damaged' => 'danger',
                                    'Maintenance' => 'warning text-dark',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">
                                {{ $asset->condition }}
                            </span>
                        </td>
                        <td class="text-muted fst-italic">{{ Str::limit($asset->maintenance_note, 30) ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-check-circle fs-1 text-success d-block mb-2"></i>
                                <span class="fw-bold">No issues found!</span><br>
                                All assets are in good condition.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($assets->hasPages())
        <div class="card-footer bg-white border-top-0 d-flex justify-content-center py-3">
            {{ $assets->links() }}
        </div>
        @endif
    </div>

</div> <!-- End Container -->

{{-- ================================================================================= --}}
{{-- SCRIPTS                                                                           --}}
{{-- ================================================================================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // ==================== 1. WEEKLY TRENDS (Line Chart) ====================
        const weeklyCtx = document.getElementById('weeklyTrendsChart').getContext('2d');
        const weeklyGradient = weeklyCtx.createLinearGradient(0, 0, 0, 300);
        weeklyGradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
        weeklyGradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

        new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: @json($weeklyTrends['labels']),
                datasets: [{
                    label: 'Bookings',
                    data: @json($weeklyTrends['data']),
                    borderColor: '#6366f1',
                    backgroundColor: weeklyGradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#f1f5f9' },
                        ticks: { stepSize: 1 }
                    },
                    x: { grid: { display: false } }
                }
            }
        });

        // ==================== 2. STATUS DISTRIBUTION (Doughnut) ====================
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: @json($statusDistribution['labels']),
                datasets: [{
                    data: @json($statusDistribution['data']),
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#6366f1', '#8b5cf6'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 15, usePointStyle: true }
                    }
                }
            }
        });

        // ==================== 3. POPULAR FACILITIES (Bar Chart) ====================
        new Chart(document.getElementById('popularityChart'), {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Bookings',
                    data: @json($chartData),
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)'
                    ],
                    borderRadius: 8,
                    barPercentage: 0.6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { 
                        beginAtZero: true, 
                        grid: { color: '#f1f5f9' },
                        ticks: { stepSize: 1 }
                    },
                    y: { grid: { display: false } }
                }
            }
        });

        // ==================== 4. PEAK HOURS (Gradient Bar Chart) ====================
        const peakCtx = document.getElementById('peakHoursChart').getContext('2d');
        const peakGradient = peakCtx.createLinearGradient(0, 0, 0, 280);
        peakGradient.addColorStop(0, 'rgba(16, 185, 129, 0.9)');
        peakGradient.addColorStop(1, 'rgba(16, 185, 129, 0.3)');

        new Chart(peakCtx, {
            type: 'bar',
            data: {
                labels: @json($peakHours['labels']),
                datasets: [{
                    label: 'Bookings',
                    data: @json($peakHours['data']),
                    backgroundColor: peakGradient,
                    borderRadius: 4,
                    barPercentage: 0.7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#f1f5f9' },
                        ticks: { stepSize: 1 }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { maxRotation: 45, minRotation: 45 }
                    }
                }
            }
        });

        // ==================== 5. USER ROLES (Doughnut) ====================
        new Chart(document.getElementById('userRolesChart'), {
            type: 'doughnut',
            data: {
                labels: @json($userRoles['labels']),
                datasets: [{
                    data: @json($userRoles['data']),
                    backgroundColor: ['#3b82f6', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 12, usePointStyle: true }
                    }
                }
            }
        });

        // ==================== 6. LIVE CLOCK ====================
        setInterval(() => {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            const clockEl = document.getElementById('live-clock');
            if(clockEl) clockEl.innerText = timeString;
        }, 1000);
    });
</script>
@endsection