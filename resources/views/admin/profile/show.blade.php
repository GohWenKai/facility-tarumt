@extends('layouts.admin')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>My Admin Profile</h1>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: User Info & Credits -->
        <div class="col-md-4">

            <!-- Personal Info Card -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    Personal Details
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Name:</strong> <br> {{ $user->name }}
                    </li>
                    <li class="list-group-item">
                        <strong>TARUMT ID:</strong> <br> {{ $user->tarumt_id ?? 'N/A' }}
                    </li>
                    <li class="list-group-item">
                        <strong>Email:</strong> <br> {{ $user->email }}
                    </li>
                    <li class="list-group-item">
                        <strong>Role:</strong> <br> 
                        <span class="badge bg-danger">{{ ucfirst($user->role) }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Address:</strong> <br> {{ $user->address }}
                    </li>
                    <li class="list-group-item">
                        <strong>Phone Number:</strong> <br> {{ $user->tel }}
                    </li>
                    <li class="list-group-item">
                        <strong>Member Since:</strong> <br> {{ $user->created_at->format('d M Y') }}
                    </li>
                    <li class="list-group-item text-center">
                        <!-- UPDATED ROUTE: Points to admin.profile.edit -->
                        <a href="{{ route('admin.profile.edit') }}" class="btn btn-warning w-100">
                            <strong>Edit Profile</strong>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right Column: Recent Activity -->
<div class="col-md-8">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent System Bookings</h5>
            <small>Last 5 entries</small>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Facility</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($recentBookings) && $recentBookings->count() > 0)
                            @foreach($recentBookings as $booking)
                            <tr>
                                <!-- ID -->
                                <td>
                                    <strong>#{{ $booking->id }}</strong>
                                </td>
                                
                                <!-- User -->
                                <td>
                                    <span class="fw-bold">{{ $booking->user ? $booking->user->name : 'Unknown' }}</span>
                                    <br>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        {{ $booking->user ? $booking->user->role : '-' }}
                                    </small>
                                </td>

                                <!-- Facility -->
                                <td>
                                    {{ $booking->facility ? $booking->facility->name : 'Deleted' }}
                                </td>

                                <!-- Date -->
                                <td>
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('M d') }}
                                    <br>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}
                                    </small>
                                </td>

                                <!-- Status Badge -->
                                <td>
                                    @php
                                        $badge = 'secondary';
                                        if($booking->status == 'approved') $badge = 'success';
                                        if($booking->status == 'pending') $badge = 'warning text-dark';
                                        if($booking->status == 'rejected') $badge = 'danger';
                                        if($booking->status == 'cancelled') $badge = 'dark';
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>

                                <!-- Action: View Only -->
                                <td class="text-end">
                                    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No recent activity found.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Footer Link to Full Dashboard -->
        <div class="card-footer text-center bg-light">
            <a href="{{ route('dashboard') }}" class="text-decoration-none fw-bold">
                View All Bookings &rarr;
            </a>
        </div>
    </div>
</div>

    </div> <!-- End Row -->
</div> <!-- End Container -->
@endsection