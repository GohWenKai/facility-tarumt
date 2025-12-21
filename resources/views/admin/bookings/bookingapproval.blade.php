@extends('layouts.admin')

@section('content')
<style>
/* ========================================
   BOOKING APPROVALS - PREMIUM TEAL THEME
   ======================================== */

/* Hero Header */
.hero-header {
    background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
    color: white;
    padding: 2rem 2.5rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}
.hero-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    pointer-events: none;
}
.hero-title { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem; }
.hero-subtitle { opacity: 0.85; font-size: 0.95rem; margin: 0; }

/* Stats Cards */
.stats-row { display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; }
.stat-card { flex: 1; min-width: 180px; background: white; border-radius: 16px; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1rem; border: 1px solid #e2e8f0; transition: all 0.2s ease; }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
.stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
.stat-icon.pending { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706; }
.stat-icon.approved { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #059669; }
.stat-icon.rejected { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; }
.stat-icon.total { background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #4f46e5; }
.stat-content { flex: 1; }
.stat-value { font-size: 1.5rem; font-weight: 700; color: #1e293b; line-height: 1; }
.stat-label { font-size: 0.8rem; color: #64748b; margin-top: 0.25rem; }

/* Filter Tabs */
.filter-tabs { display: flex; gap: 0.5rem; background: #f8fafc; padding: 0.5rem; border-radius: 12px; margin-bottom: 1.5rem; }
.filter-tab { padding: 0.625rem 1.25rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; color: #64748b; background: transparent; border: none; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; gap: 0.5rem; }
.filter-tab:hover { background: #e2e8f0; color: #334155; }
.filter-tab.active { background: white; color: #0f766e; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.filter-tab .count { background: #e2e8f0; padding: 0.125rem 0.5rem; border-radius: 20px; font-size: 0.75rem; }
.filter-tab.active .count { background: #ccfbf1; color: #0f766e; }

/* Data Card */
.data-card { background: white; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; }
.data-card-header { display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; }
.data-card-title { display: flex; align-items: center; gap: 0.75rem; font-size: 1.1rem; font-weight: 600; color: #1e293b; margin: 0; }
.data-card-title i { color: #0f766e; }
.search-box { position: relative; width: 280px; }
.search-box input { width: 100%; padding: 0.625rem 1rem 0.625rem 2.5rem; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 0.9rem; transition: all 0.2s ease; }
.search-box input:focus { outline: none; border-color: #14b8a6; box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.1); }
.search-box i { position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: #94a3b8; }

/* Table Styling */
.booking-table { margin: 0; }
.booking-table thead { background: #f8fafc; }
.booking-table thead th { padding: 1rem 1.25rem; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border-bottom: 2px solid #e2e8f0; }
.booking-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background-color 0.15s ease; }
.booking-table tbody tr:hover { background: #f0fdfa; }
.booking-table tbody td { padding: 1rem 1.25rem; vertical-align: middle; }

/* Reference ID */
.ref-id { font-family: 'SF Mono', 'Consolas', monospace; font-size: 0.75rem; color: #64748b; background: #f1f5f9; padding: 0.25rem 0.5rem; border-radius: 6px; }

/* User Cell */
.user-cell { display: flex; align-items: center; gap: 0.875rem; }
.user-avatar { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: 700; color: white; flex-shrink: 0; }
.user-avatar.lecturer { background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); }
.user-avatar.student { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
.user-avatar.default { background: linear-gradient(135deg, #64748b 0%, #475569 100%); }
.user-name { font-weight: 600; color: #1e293b; font-size: 0.9rem; }
.user-meta { font-size: 0.75rem; color: #94a3b8; }

/* Facility Cell */
.facility-cell { display: flex; align-items: center; gap: 0.75rem; }
.facility-icon { width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%); display: flex; align-items: center; justify-content: center; color: #0f766e; }
.facility-name { font-weight: 600; color: #1e293b; font-size: 0.9rem; }
.facility-type { font-size: 0.75rem; color: #94a3b8; }

/* Schedule Cell */
.schedule-date { font-weight: 600; color: #1e293b; }
.schedule-time { font-size: 0.8rem; color: #64748b; }

/* Status Badge */
.status-badge { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.875rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
.status-badge.pending { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; }
.status-badge.approved { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #047857; }
.status-badge.rejected { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #b91c1c; }
.status-badge.cancelled { background: #f1f5f9; color: #64748b; }

/* Time Ago */
.time-ago { color: #94a3b8; font-size: 0.8rem; }

/* Action Buttons */
.action-buttons { display: flex; gap: 0.5rem; justify-content: flex-end; }
.action-btn { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border: 2px solid #e2e8f0; border-radius: 10px; background: white; color: #64748b; cursor: pointer; transition: all 0.2s ease; text-decoration: none; }
.action-btn:hover { transform: translateY(-2px); }
.action-btn.approve { border-color: #10b981; color: #10b981; background: #ecfdf5; }
.action-btn.approve:hover { background: #10b981; color: white; }
.action-btn.reject { border-color: #ef4444; color: #ef4444; background: #fef2f2; }
.action-btn.reject:hover { background: #ef4444; color: white; }
.action-btn.view:hover { border-color: #3b82f6; color: #3b82f6; background: #eff6ff; }

/* Empty State */
.empty-state { display: flex; flex-direction: column; align-items: center; padding: 4rem 2rem; text-align: center; }
.empty-icon { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #14b8a6; margin-bottom: 1rem; }
.empty-state h4 { font-weight: 600; color: #334155; margin-bottom: 0.5rem; }
.empty-state p { color: #64748b; }

/* Pagination */
.pagination-wrapper { display: flex; justify-content: center; padding: 1.5rem; border-top: 1px solid #f1f5f9; }
</style>

<div class="container-fluid px-4 py-4">
    
    <!-- Hero Header -->
    <div class="hero-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="hero-title">
                <i class="bi bi-calendar-check me-2"></i>Booking Approvals
            </h1>
            <p class="hero-subtitle">Review and manage facility booking requests</p>
        </div>
        <a href="{{ url()->current() }}" class="btn btn-light fw-bold px-4">
            <i class="bi bi-arrow-clockwise me-2"></i>Refresh
        </a>
    </div>

    <!-- Stats Cards -->
    @php
        $pendingCount = $bookings->where('status', 'pending')->count();
        $approvedCount = $bookings->where('status', 'approved')->count();
        $rejectedCount = $bookings->where('status', 'rejected')->count();
        $totalCount = $bookings->count();
    @endphp
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon pending"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ $pendingCount }}</div>
                <div class="stat-label">Pending Review</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon approved"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ $approvedCount }}</div>
                <div class="stat-label">Approved</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon rejected"><i class="bi bi-x-circle-fill"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ $rejectedCount }}</div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon total"><i class="bi bi-calendar2-week"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ $totalCount }}</div>
                <div class="stat-label">Total Requests</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="data-card">
        <div class="data-card-header">
            <h3 class="data-card-title">
                <i class="bi bi-list-task"></i>
                Request Queue
            </h3>
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Search bookings...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table booking-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>User</th>
                        <th>Facility</th>
                        <th>Schedule</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="bookingTableBody">
                    @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <span class="ref-id" title="{{ $booking->id }}">
                                #{{ Str::limit($booking->id, 8, '..') }}
                            </span>
                        </td>

                        <td>
                            <div class="user-cell">
                                @php
                                    $initial = strtoupper(substr($booking->user->name ?? 'U', 0, 1));
                                    $role = strtolower($booking->user->role ?? 'default');
                                @endphp
                                <div class="user-avatar {{ $role }}">{{ $initial }}</div>
                                <div>
                                    <div class="user-name">{{ $booking->user->name ?? 'Unknown' }}</div>
                                    <div class="user-meta">{{ ucfirst($booking->user->role ?? '-') }} • {{ $booking->user->tarumt_id ?? '' }}</div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="facility-cell">
                                <div class="facility-icon"><i class="bi bi-building"></i></div>
                                <div>
                                    <div class="facility-name">{{ $booking->facility->name ?? 'Deleted' }}</div>
                                    <div class="facility-type">{{ $booking->facility->type ?? '-' }}</div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="schedule-date">
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('M d, Y') }}
                                @if($booking->is_special_day)
                                    <span class="badge bg-warning text-dark ms-1" title="Special Day Booking">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                    </span>
                                @endif
                            </div>
                            <div class="schedule-time">{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</div>
                            @if($booking->is_special_day && $booking->special_reason)
                                <div class="mt-1">
                                    <small class="text-warning fw-bold">
                                        <i class="bi bi-chat-quote"></i> Reason:
                                    </small>
                                    <small class="text-muted" style="display: block; max-width: 200px; word-wrap: break-word;">
                                        "{{ Str::limit($booking->special_reason, 50) }}"
                                    </small>
                                </div>
                            @elseif($booking->is_special_day)
                                <small class="text-danger">
                                    <i class="bi bi-exclamation-circle"></i> No reason provided
                                </small>
                            @endif
                        </td>

                        <td>
                            @php
                                $statusClass = match($booking->status) {
                                    'approved' => 'approved',
                                    'pending' => 'pending',
                                    'rejected' => 'rejected',
                                    default => 'cancelled'
                                };
                                $statusIcon = match($booking->status) {
                                    'approved' => 'bi-check-circle-fill',
                                    'pending' => 'bi-hourglass-split',
                                    'rejected' => 'bi-x-circle-fill',
                                    default => 'bi-dash-circle'
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                <i class="bi {{ $statusIcon }}"></i>
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>

                        <td>
                            <span class="time-ago">{{ $booking->created_at->diffForHumans() }}</span>
                        </td>

                        <td>
                            <div class="action-buttons">
                                @if($booking->status == 'pending')
                                    <form action="{{ route('admin.bookings.approve', $booking->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="action-btn approve" title="Approve">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($booking->status == 'pending' || $booking->status == 'approved')
                                    <form action="{{ route('admin.bookings.reject', $booking->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="action-btn reject" title="Reject" onclick="return confirm('Reject this booking?')">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="action-btn view" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="bi bi-calendar-check"></i></div>
                                <h4>No Booking Requests</h4>
                                <p>All caught up! No booking requests to review.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($bookings->hasPages())
        <div class="pagination-wrapper">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
</div>

<script>
// Client-side search filter
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#bookingTableBody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>
@endsection