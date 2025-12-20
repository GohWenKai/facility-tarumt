@extends('layouts.app')

@section('content')
<style>
/* ========================================
   BOOKING HISTORY - MODERN HCI DESIGN
   ======================================== */

/* Page Header */
.page-header {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    color: white;
    padding: 2rem 2.5rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}
.page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
}
.page-title { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem; }
.page-subtitle { opacity: 0.85; font-size: 0.95rem; margin: 0; }

/* Stats Cards */
.stats-row { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.stat-mini { flex: 1; min-width: 140px; background: white; border-radius: 14px; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.875rem; border: 1px solid #e2e8f0; transition: all 0.2s ease; }
.stat-mini:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
.stat-mini-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.stat-mini-icon.total { background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #4f46e5; }
.stat-mini-icon.pending { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706; }
.stat-mini-icon.approved { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #059669; }
.stat-mini-icon.rejected { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; }
.stat-mini-value { font-size: 1.25rem; font-weight: 700; color: #1e293b; line-height: 1; }
.stat-mini-label { font-size: 0.75rem; color: #64748b; }

/* Filter Card */
.filter-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; }
.filter-title { font-size: 0.9rem; font-weight: 600; color: #1e293b; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
.filter-title i { color: #3b82f6; }
.filter-row { display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end; }
.filter-group { flex: 1; min-width: 180px; }
.filter-label { font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; display: block; }
.filter-select, .filter-input { width: 100%; padding: 0.75rem 1rem; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 0.9rem; transition: all 0.2s ease; background: #f8fafc; }
.filter-select:focus, .filter-input:focus { outline: none; border-color: #3b82f6; background: white; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
.btn-filter { padding: 0.75rem 1.5rem; border: none; border-radius: 10px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; font-weight: 600; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; gap: 0.5rem; }
.btn-filter:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(59, 130, 246, 0.35); }

/* Table Card */
.table-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
.history-table { width: 100%; margin: 0; border-collapse: collapse; }
.history-table thead { background: #f8fafc; }
.history-table thead th { padding: 1rem 1.25rem; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border-bottom: 2px solid #e2e8f0; text-align: left; }
.history-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background-color 0.15s ease; }
.history-table tbody tr:hover { background: #f8fafc; }
.history-table tbody tr:last-child { border-bottom: none; }
.history-table tbody td { padding: 1rem 1.25rem; vertical-align: middle; }

/* Facility Cell */
.facility-info { display: flex; align-items: center; gap: 0.875rem; }
.facility-icon { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 1.1rem; }
.facility-name { font-weight: 600; color: #1e293b; font-size: 0.9rem; }

/* Date & Time */
.date-cell { font-weight: 600; color: #1e293b; }
.time-cell { color: #64748b; font-size: 0.9rem; }

/* Status Badge */
.status-badge { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.875rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
.status-badge.pending { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; }
.status-badge.approved { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #047857; }
.status-badge.rejected { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #b91c1c; }
.status-badge.overdue { background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%); color: #7e22ce; }
.status-badge.completed { background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #4338ca; }
.status-badge.cancelled { background: #f1f5f9; color: #64748b; }

/* Action Buttons */
.btn-action { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; border-radius: 8px; font-size: 0.8rem; font-weight: 600; text-decoration: none; transition: all 0.2s ease; border: none; cursor: pointer; }
.btn-ticket { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
.btn-ticket:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35); color: white; }
.btn-cancel { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; }
.btn-cancel:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35); }

/* Empty State */
.empty-state { text-align: center; padding: 3rem 2rem; color: #94a3b8; }
.empty-state i { font-size: 3rem; opacity: 0.5; margin-bottom: 1rem; }
.empty-state h5 { color: #64748b; font-weight: 600; margin-bottom: 0.5rem; }
.empty-state p { font-size: 0.9rem; }

/* Loading State */
.loading-state { text-align: center; padding: 2rem; color: #64748b; }
</style>

<div class="container py-4">
    
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"><i class="bi bi-clock-history me-2"></i>My Booking History</h1>
            <p class="page-subtitle">View and manage your facility reservations</p>
        </div>
        <a href="{{ route('facilities.index') }}" class="btn btn-light fw-bold">
            <i class="bi bi-plus-lg me-1"></i> New Booking
        </a>
    </div>

    <!-- Quick Stats -->
    @php
        $totalCount = $bookings->count();
        $pendingCount = $bookings->where('status', 'pending')->count();
        $approvedCount = $bookings->where('status', 'approved')->count();
        $rejectedCount = $bookings->where('status', 'rejected')->count();
    @endphp
    <div class="stats-row">
        <div class="stat-mini">
            <div class="stat-mini-icon total"><i class="bi bi-calendar2-week"></i></div>
            <div>
                <div class="stat-mini-value">{{ $totalCount }}</div>
                <div class="stat-mini-label">Total</div>
            </div>
        </div>
        <div class="stat-mini">
            <div class="stat-mini-icon pending"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="stat-mini-value">{{ $pendingCount }}</div>
                <div class="stat-mini-label">Pending</div>
            </div>
        </div>
        <div class="stat-mini">
            <div class="stat-mini-icon approved"><i class="bi bi-check-circle-fill"></i></div>
            <div>
                <div class="stat-mini-value">{{ $approvedCount }}</div>
                <div class="stat-mini-label">Approved</div>
            </div>
        </div>
        <div class="stat-mini">
            <div class="stat-mini-icon rejected"><i class="bi bi-x-circle-fill"></i></div>
            <div>
                <div class="stat-mini-value">{{ $rejectedCount }}</div>
                <div class="stat-mini-label">Rejected</div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="filter-card">
        <h5 class="filter-title"><i class="bi bi-funnel"></i> Filter Bookings</h5>
        <div class="filter-row">
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select id="statusFilter" class="filter-select">
                    <option value="All">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Date</label>
                <input type="date" id="dateFilter" class="filter-input">
            </div>
            <div>
                <button type="button" onclick="filterHistoryJSON()" class="btn-filter">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="table-card">
        <table class="history-table">
            <thead>
                <tr>
                    <th>Facility</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="historyTableBody">
                @forelse($bookings as $booking)
                <tr>
                    <td>
                        <div class="facility-info">
                            <div class="facility-icon"><i class="bi bi-building"></i></div>
                            <span class="facility-name">{{ $booking->facility->name }}</span>
                        </div>
                    </td>
                    <td class="date-cell">{{ \Carbon\Carbon::parse($booking->start_time)->format('M d, Y') }}</td>
                    <td class="time-cell">
                        {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - 
                        {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
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
                                'overdue' => 'bi-exclamation-circle-fill',
                                'completed' => 'bi-flag-fill',
                                default => 'bi-dash-circle'
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            <i class="bi {{ $statusIcon }}"></i>
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td>
                        @if($booking->status === 'approved')
                            <a href="{{ route('booking.ticket', $booking->id) }}" class="btn-action btn-ticket">
                                <i class="bi bi-ticket-perforated"></i> Ticket
                            </a>
                        @elseif($booking->status === 'pending')
                            <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" 
                                  onsubmit="return confirm('Cancel this booking? Credits will be refunded.');" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-action btn-cancel">
                                    <i class="bi bi-x-lg"></i> Cancel
                                </button>
                            </form>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="bi bi-calendar-x"></i>
                            <h5>No Bookings Yet</h5>
                            <p>Book a facility to see your history here.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function filterHistoryJSON() {
    const status = document.getElementById('statusFilter').value;
    const date = document.getElementById('dateFilter').value;
    const tableBody = document.getElementById('historyTableBody');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    tableBody.innerHTML = '<tr><td colspan="5"><div class="loading-state"><i class="bi bi-arrow-repeat"></i> Filtering...</div></td></tr>';

    const params = new URLSearchParams({ status, date });

    fetch(`/bookings/search?${params.toString()}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error("Server Error");
        return response.json();
    })
    .then(data => {
        tableBody.innerHTML = '';
        const bookings = data.data ? data.data : data;

        if (bookings.length === 0) {
            tableBody.innerHTML = `
                <tr><td colspan="5">
                    <div class="empty-state">
                        <i class="bi bi-search"></i>
                        <h5>No Results Found</h5>
                        <p>Try adjusting your filters.</p>
                    </div>
                </td></tr>`;
            return;
        }

        bookings.forEach(booking => {
            const statusClass = booking.status === 'approved' ? 'approved' : 
                               booking.status === 'pending' ? 'pending' : 
                               booking.status === 'rejected' ? 'rejected' : 
                               booking.status === 'overdue' ? 'overdue' : 
                               booking.status === 'completed' ? 'completed' : 'cancelled';
            
            const statusIcon = booking.status === 'approved' ? 'bi-check-circle-fill' : 
                              booking.status === 'pending' ? 'bi-hourglass-split' : 
                              booking.status === 'rejected' ? 'bi-x-circle-fill' : 
                              booking.status === 'overdue' ? 'bi-exclamation-circle-fill' : 
                              booking.status === 'completed' ? 'bi-flag-fill' : 'bi-dash-circle';

            const start = new Date(booking.start_time);
            const end = new Date(booking.end_time);
            const dateStr = start.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            const startTime = start.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            const endTime = end.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            const facilityName = booking.facility ? booking.facility.name : 'Unknown';

            let actionHtml = '<span class="text-muted">—</span>';
            if (booking.status === 'approved') {
                actionHtml = `<a href="/booking/${booking.id}/ticket" class="btn-action btn-ticket">
                    <i class="bi bi-ticket-perforated"></i> Ticket
                </a>`;
            } else if (booking.status === 'pending') {
                actionHtml = `
                    <form action="/booking/${booking.id}/cancel" method="POST" 
                          onsubmit="return confirm('Cancel this booking? Credits will be refunded.')">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <button type="submit" class="btn-action btn-cancel">
                            <i class="bi bi-x-lg"></i> Cancel
                        </button>
                    </form>`;
            }

            tableBody.innerHTML += `
                <tr>
                    <td>
                        <div class="facility-info">
                            <div class="facility-icon"><i class="bi bi-building"></i></div>
                            <span class="facility-name">${facilityName}</span>
                        </div>
                    </td>
                    <td class="date-cell">${dateStr}</td>
                    <td class="time-cell">${startTime} - ${endTime}</td>
                    <td>
                        <span class="status-badge ${statusClass}">
                            <i class="bi ${statusIcon}"></i>
                            ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                        </span>
                    </td>
                    <td>${actionHtml}</td>
                </tr>`;
        });
    })
    .catch(error => {
        console.error('Error:', error);
        tableBody.innerHTML = `
            <tr><td colspan="5">
                <div class="empty-state">
                    <i class="bi bi-exclamation-triangle text-danger"></i>
                    <h5>Error Loading Data</h5>
                    <p>Please try again later.</p>
                </div>
            </td></tr>`;
    });
}
</script>
@endsection