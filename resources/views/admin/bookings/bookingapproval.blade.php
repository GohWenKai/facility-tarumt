@extends('layouts.admin')

@section('content')

<div class="container">
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <div>
                <h1>Bookings</h1>
                <p class="text-muted">Welcome, <strong>{{ Auth::user()->name }}</strong> ({{ ucfirst(Auth::user()->role) }})</p>
            </div>
            
            <!-- Refresh Button -->
            <a href="{{ url()->current() }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-clockwise"></i> Refresh Queue
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Latest Bookings</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#ID</th>
                                    <th>User Name</th>
                                    <th>Role</th>
                                    <th>Facility</th>
                                    <th>Booking Date</th>
                                    <th>Time Slot</th>
                                    <th>Status</th>
                                    <th>Submitted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($bookings->count() > 0)
                                    @foreach($bookings as $booking)
                                    
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.bookings.show', $booking->id) }}" class="fw-bold text-decoration-none">
                                                <strong>{{ $booking->id }}</strong>
                                            </a>
                                        </td>
                                        
                                        <!-- User Column -->
                                        <td>
                                            {{ $booking->user ? $booking->user->name : 'Unknown User' }}
                                            <br>
                                            <small class="text-muted">{{ $booking->user ? $booking->user->tarumt_id : '-' }}</small>
                                        </td>

                                        <!-- User Role Column -->
                                        <td>
                                            {{ $booking->user ? $booking->user->role : 'Unknown User' }}
                                        </td>

                                        <!-- Facility Column -->
                                        <td>
                                            {{ $booking->facility ? $booking->facility->name : 'Deleted Facility' }}
                                        </td>

                                        <!-- Date Column -->
                                        <td>
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('Y-m-d') }}
                                        </td>

                                        <!-- Time Column -->
                                        <td>
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                        </td>

                                        <!-- Status Column -->
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

                                        <!-- Queue Time (Created At) -->
                                        <td>
                                            <small>{{ $booking->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">                   
                                                {{-- SHOW APPROVE BUTTON (Unless it is already approved) --}}
                                                @if($booking->status == 'pending')
                                                <form action="{{ route('admin.bookings.approve', $booking->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Set to Approved">
                                                        <i class="bi bi-check-lg"></i> Approve
                                                    </button>
                                                </form>
                                                @endif

                                            {{-- SHOW REJECT BUTTON (Unless it is already rejected) --}}
                                            @if($booking->status == 'pending' || $booking->status == 'approved')
                                            <form action="{{ route('admin.bookings.reject', $booking->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Set to Rejected" 
                                                        onclick="return confirm('Change status to REJECTED?')">
                                                        <i class="bi bi-x-lg"></i> Reject
                                                </button>
                                            </form>
                                            @endif

                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            No bookings found in the queue.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection