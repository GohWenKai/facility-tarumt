@extends('layouts.app')

@section('content')
<div class="container">
    <h2>My Booking History</h2>

    <!-- Filter Controls (Unchanged) -->
    <div class="card mb-4 p-3 bg-light">
        <h5>Filter History</h5>
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select id="statusFilter" class="form-select">
                    <option value="All">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Date</label>
                <input type="date" id="dateFilter" class="form-control">
            </div>
            <div class="col-md-4">
                <button type="button" onclick="filterHistoryJSON()" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter Results
                </button>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Facility</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="historyTableBody">
                {{-- 1. INITIAL LOAD (Server Side) --}}
                @foreach($bookings as $booking)
                <tr>
                    <td>{{ $booking->facility->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('Y-m-d') }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                    </td>
                    <td>
                        <span class="badge bg-{{ $booking->status == 'approved' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td>
                        @if($booking->status === 'approved')
                            {{-- DOWNLOAD TICKET --}}
                            <a href="{{ route('booking.ticket', $booking->id) }}" class="btn btn-success btn-sm">
                                <i class="bi bi-file-earmark-pdf"></i> Ticket
                            </a>

                        @elseif($booking->status === 'pending')
                            {{-- CANCEL BUTTON (Form required for security) --}}
                            <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Cancel this booking? Credits will be refunded.');" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </button>
                            </form>

                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- JAVASCRIPT SECTION -->
<script>
function filterHistoryJSON() {
    const status = document.getElementById('statusFilter').value;
    const date = document.getElementById('dateFilter').value;
    const tableBody = document.getElementById('historyTableBody');
    
    // Get CSRF Token for the JavaScript-generated forms
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Show Loading...
    tableBody.innerHTML = '<tr><td colspan="5" class="text-center">Filtering...</td></tr>';

    const params = new URLSearchParams({
        status: status,
        date: date
    });

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
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center">No bookings found.</td></tr>';
            return;
        }

        bookings.forEach(booking => {
            let badgeColor = 'secondary';
            if(booking.status === 'approved') badgeColor = 'success';
            if(booking.status === 'pending') badgeColor = 'warning';
            if(booking.status === 'rejected') badgeColor = 'danger';

            const start = new Date(booking.start_time);
            const end = new Date(booking.end_time);
            const dateStr = start.toISOString().split('T')[0];
            const timeStr = `${start.getHours()}:${String(start.getMinutes()).padStart(2,'0')} - ${end.getHours()}:${String(end.getMinutes()).padStart(2,'0')}`;

            const facilityName = booking.facility ? booking.facility.name : 'Unknown';

            // --- ACTION BUTTON LOGIC ---
            let actionHtml = '<span class="text-muted">-</span>';
            
            if(booking.status === 'approved') {
                // Show Ticket Button
                actionHtml = `<a href="/booking/${booking.id}/ticket" class="btn btn-success btn-sm">
                                <i class="bi bi-file-earmark-pdf"></i> Ticket
                              </a>`;
            } else if(booking.status === 'pending') {
                // Show Cancel Button (Form)
                actionHtml = `
                    <form action="/booking/${booking.id}/cancel" method="POST" onsubmit="return confirm('Cancel this booking? Credits will be refunded.')">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                    </form>
                `;
            }

            const row = `
                <tr>
                    <td>${facilityName}</td>
                    <td>${dateStr}</td>
                    <td>${timeStr}</td>
                    <td>
                        <span class="badge bg-${badgeColor}">
                            ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                        </span>
                    </td>
                    <td>${actionHtml}</td>
                </tr>
            `;
            
            tableBody.innerHTML += row;
        });
    })
    .catch(error => {
        console.error('Error:', error);
        tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error Happening.</td></tr>';
    });
}
</script>
@endsection