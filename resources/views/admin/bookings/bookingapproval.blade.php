@extends('layouts.admin')

@section('content')

{{-- EMBEDDED CSS for Premium Look --}}
<style>
    .page-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
        padding: 2.5rem 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .page-header::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-image: radial-gradient(circle at 100% 100%, rgba(255,255,255,0.1) 0, transparent 20%);
        pointer-events: none;
    }
    
    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        transition: transform 0.2s;
    }
    .stats-card:hover {
        transform: translateY(-2px);
    }

    .table-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        border: none;
        overflow: hidden;
    }
    
    .modern-table thead th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .modern-table tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        font-size: 0.9rem;
    }
    
    /* User Avatar */
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
        font-size: 0.9rem;
    }
    
    /* Status Badges */
    .status-badge {
        padding: 0.5em 1em;
        border-radius: 50rem;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .badge-approved { background-color: #dcfce7; color: #166534; }
    .badge-pending { background-color: #fef9c3; color: #854d0e; }
    .badge-rejected { background-color: #fee2e2; color: #991b1b; }
    .badge-cancelled { background-color: #f1f5f9; color: #475569; }

    /* Action Buttons */
    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    .btn-icon:hover {
        background-color: #f1f5f9;
        transform: scale(1.05);
    }
    .btn-approve { color: #10b981; background-color: #ecfdf5; border-color: #10b981; }
    .btn-reject { color: #ef4444; background-color: #fef2f2; border-color: #ef4444; }
    
     /* ID Pill */
    .id-pill {
        font-family: 'Courier New', monospace;
        background: #f1f5f9;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.8em;
        color: #475569;
    }
</style>

<div class="container-fluid px-4">
    
    {{-- 1. HERO HEADER --}}
    <div class="page-header d-flex justify-content-between align-items-center mt-4">
        <div>
            <h2 class="fw-bold mb-1">Booking Approvals</h2>
            <p class="mb-0 opacity-75">Manage usage requests and facility allocations</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ url()->current() }}" class="btn btn-light text-dark fw-bold shadow-sm">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh List
            </a>
        </div>
    </div>

    {{-- 2. MAIN CONTENT TABLE --}}
    <div class="row">
        <div class="col-12">
            <div class="table-card">
                
                {{-- Toolbar / Filters (Visual Only for now) --}}
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-white">
                    <h5 class="fw-bold m-0 text-dark">
                        <i class="bi bi-list-check text-primary me-2"></i>Request Queue
                    </h5>
                    <div class="d-flex gap-2">
                        <!-- Search (Client-side conceptual) -->
                        <div class="input-group" style="width: 250px;">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" placeholder="Search requests...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table modern-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Reference</th>
                                <th>User Profile</th>
                                <th>Facility details</th>
                                <th>Schedule</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                            <tr>
                                {{-- 1. Reference ID --}}
                                <td>
                                    <span class="id-pill" title="{{ $booking->id }}">
                                        {{ substr($booking->id, 0, 8) }}..
                                    </span>
                                </td>

                                {{-- 2. User Profile --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        @php
                                            $initials = strtoupper(substr($booking->user->name ?? '?', 0, 1));
                                            $bgColors = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'];
                                            $bg = $bgColors[rand(0, 4)];
                                        @endphp
                                        <div class="avatar-circle shadow-sm me-3" style="background: {{ $bg }}">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $booking->user->name ?? 'Unknown' }}</div>
                                            <div class="small text-muted">{{ $booking->user->role ?? '-' }} • {{ $booking->user->tarumt_id ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- 3. Facility --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded p-2 me-2">
                                            <i class="bi bi-building text-secondary"></i>
                                        </div>
                                        <div>
                                            <span class="d-block fw-bold text-dark">{{ $booking->facility->name ?? 'Deleted' }}</span>
                                            <span class="small text-muted">{{ $booking->facility->location ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- 4. Schedule --}}
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('M d, Y') }}
                                        </span>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - 
                                            {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                        </small>
                                    </div>
                                </td>

                                {{-- 5. Status Badge --}}
                                <td>
                                    @php
                                        $statusConfig = match($booking->status) {
                                            'approved' => ['class' => 'badge-approved', 'icon' => 'bi-check-circle-fill', 'label' => 'Approved'],
                                            'pending'  => ['class' => 'badge-pending',  'icon' => 'bi-hourglass-split',  'label' => 'Pending'],
                                            'rejected' => ['class' => 'badge-rejected', 'icon' => 'bi-x-circle-fill',    'label' => 'Rejected'],
                                            default    => ['class' => 'badge-cancelled', 'icon' => 'bi-dash-circle',      'label' => ucfirst($booking->status)]
                                        };
                                    @endphp
                                    <span class="status-badge {{ $statusConfig['class'] }}">
                                        <i class="bi {{ $statusConfig['icon'] }}"></i>
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </td>

                                {{-- 6. Submitted --}}
                                <td class="text-muted small">
                                    {{ $booking->created_at->diffForHumans() }}
                                </td>

                                {{-- 7. Actions --}}
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        {{-- Approve --}}
                                        @if($booking->status == 'pending')
                                            <form action="{{ route('admin.bookings.approve', $booking->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn-icon btn-approve" title="Approve Request" data-bs-toggle="tooltip">
                                                    <i class="bi bi-check-lg fs-5"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Reject --}}
                                        @if($booking->status == 'pending' || $booking->status == 'approved')
                                            <form action="{{ route('admin.bookings.reject', $booking->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn-icon btn-reject" title="Reject Request" 
                                                        onclick="return confirm('Are you sure you want to reject this booking?')" data-bs-toggle="tooltip">
                                                    <i class="bi bi-x-lg fs-6"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        {{-- View Details --}}
                                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn-icon bg-light text-secondary border" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="opacity-50 mb-3">
                                        <i class="bi bi-inbox fs-1"></i>
                                    </div>
                                    <h6 class="fw-bold text-muted">No booking requests found</h6>
                                    <p class="small text-muted">All caught up! Check back later.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination Links --}}
                <div class="d-flex justify-content-center py-4">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tooltip Script --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>

@endsection