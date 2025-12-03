@extends('layouts.admin')

@section('content')
<div class="container">
    {{-- Breadcrumb / Back Button --}}
    <div class="mb-4">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Booking Details #{{ $booking->id }}</h4>
                    
                    {{-- Status Badge --}}
                    @php
                        $badgeColor = match($booking->status) {
                            'approved' => 'success',
                            'rejected' => 'danger',
                            'pending'  => 'warning text-dark',
                            default    => 'secondary'
                        };
                    @endphp
                    <span class="badge bg-{{ $badgeColor }} fs-6">{{ ucfirst($booking->status) }}</span>
                </div>

                <div class="card-body">
                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="row g-4">
                        {{-- 1. Student Details --}}
                        <div class="col-md-6 border-end">
                            <h5 class="text-primary mb-3"><i class="bi bi-person-circle"></i> User Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th class="w-25 text-muted">Name:</th>
                                    <td>{{ $booking->user->name ?? 'Unknown User' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">ID:</th>
                                    <td>{{ $booking->user->tarumt_id ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Email:</th>
                                    <td>{{ $booking->user->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Role:</th>
                                    <td><span class="badge bg-info text-dark">{{ ucfirst($booking->user->role ?? 'N/A') }}</span></td>
                                </tr>
                            </table>
                        </div>

                        {{-- 2. Facility Details --}}
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3"><i class="bi bi-building"></i> Facility Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th class="w-25 text-muted">Facility:</th>
                                    <td class="fw-bold">{{ $booking->facility->name ?? 'Deleted Facility' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Type:</th>
                                    <td>{{ $booking->facility->type ?? 'General' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Location:</th>
                                    <td>{{ $booking->facility->location ?? 'Main Campus' }}</td>
                                </tr>
                            </table>
                        </div>

                        <hr>

                        {{-- 3. Timing Details --}}
                        <div class="col-md-12">
                            <h5 class="text-primary mb-3"><i class="bi bi-calendar-event"></i> Booking Schedule</h5>
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded border">
                                        <small class="text-muted d-block">Date</small>
                                        <strong>{{ \Carbon\Carbon::parse($booking->start_time)->format('D, d M Y') }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded border">
                                        <small class="text-muted d-block">Time Slot</small>
                                        <strong>
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - 
                                            {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                        </strong>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded border">
                                        <small class="text-muted d-block">Request Created</small>
                                        {{ $booking->created_at->format('d M Y, h:i A') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    {{-- 4. Admin Actions Footer --}}
                    <div class="card-footer bg-light p-3">
                        <h6 class="text-muted mb-3">Admin Actions:</h6>
                        
                        @if($booking->status === 'pending')
                            {{-- CASE 1: PENDING (Show Both Options) --}}
                            <div class="d-flex gap-3">
                                {{-- Approve --}}
                                <form action="{{ route('admin.bookings.approve', $booking->id) }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100 btn-lg">
                                        <i class="bi bi-check-circle-fill"></i> Approve
                                    </button>
                                </form>

                                {{-- Reject --}}
                                <form action="{{ route('admin.bookings.reject', $booking->id) }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100 btn-lg" onclick="return confirm('Reject this booking? Credits will be refunded.')">
                                        <i class="bi bi-x-circle-fill"></i> Reject
                                    </button>
                                </form>
                            </div>

                        @elseif($booking->status === 'approved')
                            {{-- CASE 2: APPROVED (Show Only Revoke/Cancel) --}}
                            <div class="alert alert-success d-flex justify-content-between align-items-center mb-0">
                                <span>
                                    <i class="bi bi-check-circle"></i> This booking is currently <strong>Active</strong>. Ticket has been generated.
                                </span>
                                
                                {{-- Emergency Revoke --}}
                                <form action="{{ route('admin.bookings.reject', $booking->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('WARNING: This will delete the XML ticket and refund the user. Are you sure?')">
                                        <i class="bi bi-exclamation-triangle"></i> Revoke / Cancel
                                    </button>
                                </form>
                            </div>

                        @else
                            {{-- CASE 3: REJECTED (Show Nothing or Info) --}}
                            <div class="alert alert-secondary mb-0 text-center">
                                <i class="bi bi-lock-fill"></i> This booking is <strong>Closed</strong> (Rejected). No further actions allowed.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection