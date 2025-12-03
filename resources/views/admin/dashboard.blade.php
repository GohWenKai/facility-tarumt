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
</style>

<div class="container-fluid px-4 py-4" style="background-color: #f8fafc; min-height: 100vh;">

    {{-- ================================================================================= --}}
    {{-- SECTION 1: ANALYTICS (Chart)                                                      --}}
    {{-- ================================================================================= --}}
    <div class="row">
        <div class="col-md-12">
            <div class="dashboard-card">
                <div class="dashboard-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark m-0">
                            <i class="bi bi-bar-chart-fill text-primary me-2"></i>Analytics Overview
                        </h5>
                        <small class="text-muted">Top 5 most booked facilities</small>
                    </div>
                </div>
                <div class="card-body">
                    <div style="height: 300px; width: 100%;">
                        <canvas id="popularityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================================= --}}
    {{-- SECTION 2: BOOKING SCHEDULE (Main Tool)                                           --}}
    {{-- ================================================================================= --}}
    
@php
    // define time slots
    $startHour = 8; 
    $endHour = 22; 
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
        // 1. Chart Logic
        const ctx = document.getElementById('popularityChart').getContext('2d');
        const labels = @json($chartLabels);
        const data = @json($chartData);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Bookings',
                    data: data,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)', 
                        'rgba(16, 185, 129, 0.7)', 
                        'rgba(245, 158, 11, 0.7)', 
                        'rgba(239, 68, 68, 0.7)',  
                        'rgba(139, 92, 246, 0.7)'  
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(139, 92, 246, 1)'
                    ],
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                }
            }
        });

        // 2. Live Clock
        setInterval(() => {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            const clockEl = document.getElementById('live-clock');
            if(clockEl) clockEl.innerText = timeString;
        }, 1000);
    });
</script>
@endsection