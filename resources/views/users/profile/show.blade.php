@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>My Profile</h1>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: User Info & Credits -->
        <div class="col-md-4">
            
            <!-- Credit Card -->
            <div class="card text-white bg-primary mb-4 shadow">
                <div class="card-body text-center">
                    <h5 class="card-title">Available Credits</h5>
                    <h1 class="display-4 fw-bold">{{ $user->credits }}</h1>
                    <p class="card-text">Credits reset every Sunday.</p>
                </div>
            </div>

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
                        <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
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
                    <li class="list-group-item">
                        <a href="{{ route('profile.edit') }}" class="btn btn-warning"><strong>Edit Profile</strong></a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right Column: Booking History -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Booking History</h5>
                    <a href="{{ route('facilities.index') }}" class="btn btn-sm btn-success">New Booking</a>
                </div>
                <div class="card-body">
                    @if($bookings->isEmpty())
                        <div class="alert alert-info">
                            You haven't made any bookings yet.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Facility</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Cost</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                    <tr>
                                        <td>
                                            <strong>{{ $booking->facility->name }}</strong><br>
                                            <small class="text-muted">{{ $booking->facility->building->name ?? 'Main Campus' }}</small>
                                        </td>
                                        <td>
                                            {{ $booking->start_time->format('d M Y') }}
                                        </td>
                                        <td>
                                            {{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}
                                        </td>
                                        <td>
                                            {{ $booking->total_cost }} pts
                                        </td>
                                        <td>
                                            @if($booking->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($booking->status == 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($booking->status == 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $booking->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination Links -->
                        <div class="mt-3">
                            {{ $bookings->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection