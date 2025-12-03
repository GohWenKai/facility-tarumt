@extends('layouts.app')

@php
    $startH = (int) \Carbon\Carbon::parse($facility->start_time)->format('H');
    $endH   = (int) \Carbon\Carbon::parse($facility->end_time)->format('H');
    $openTime  = \Carbon\Carbon::parse($facility->start_time)->format('H:i');
    $closeTime = \Carbon\Carbon::parse($facility->end_time)->format('H:i');
@endphp

@section('content')
<div class="container">
    
    <!-- 1. BROKEN ASSET WARNING (New Addition) -->
    @if(isset($brokenAssets) && $brokenAssets->isNotEmpty())
        <div class="alert alert-warning border-warning shadow-sm mb-4">
            <h5 class="alert-heading text-dark"><i class="bi bi-exclamation-triangle-fill text-danger"></i> Equipment Alert</h5>
            <p class="mb-1 text-dark">The following equipment in this facility is currently unavailable:</p>
            <ul class="mb-0">
                @foreach($brokenAssets as $asset)
                    <li class="text-danger fw-bold">
                        {{ $asset->name }} 
                        <span class="badge bg-danger">{{ $asset->condition }}</span>
                        @if($asset->maintenance_note)
                            <small class="text-muted fst-italic">({{ $asset->maintenance_note }})</small>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Left: Facility Details -->
        <div class="col-md-6">
            <!-- Fixed Image Path logic -->
            <div style="max-height: 400px; overflow: hidden;" class="rounded mb-3">
                @if($facility->image_path)
                    <img src="{{ asset('storage/' . $facility->image_path) }}" class="img-fluid w-100 object-fit-cover" alt="{{ $facility->name }}">
                @else
                    <img src="https://via.placeholder.com/600x400?text=No+Image" class="img-fluid w-100">
                @endif
            </div>
            
            <h2>{{ $facility->name }}</h2>
            <p class="lead text-muted">{{ $facility->building->name }}</p>
            
            <!-- General Details -->
            <div class="card mb-3">
                <div class="card-header bg-light fw-bold">Facility Details</div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li><strong>Type:</strong> {{ $facility->type }}</li>
                        <li><strong>Capacity:</strong> {{ $facility->capacity }} Pax</li>
                        <li><strong>Status:</strong> 
                            <span class="badge bg-{{ $facility->status == 'Available' ? 'success' : 'danger' }}">
                                {{ ucfirst($facility->status) }}
                            </span>
                        </li>
                        <li><strong>Hours:</strong> {{ $openTime }} - {{ $closeTime }}</li>
                    </ul>
                </div>
            </div>

            <!-- 2. ASSET LIST (New Addition) -->
            <div class="card mb-3">
                <div class="card-header bg-light fw-bold">Assets & Equipment</div>
                <ul class="list-group list-group-flush">
                    @forelse($facility->assets as $asset)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $asset->name }}</strong>
                                <br><small class="text-muted">{{ $asset->type }} ({{ $asset->serial_number }})</small>
                            </div>
                            <!-- Condition Badge -->
                            @php
                                $badgeClass = match($asset->condition) {
                                    'Good' => 'success',
                                    'Fair' => 'info',
                                    'Damaged', 'Maintenance' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ $asset->condition }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted fst-italic">No assets listed for this facility.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Right: Booking Form -->
        <div class="col-md-6">
            <div class="card shadow-lg sticky-top" style="top: 20px; z-index: 1;">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Book This Room</h4>
                </div>
                <div class="card-body">
                    
                    <button type="button" class="btn btn-info w-100 mb-4 fw-bold text-white" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                        <i class="bi bi-calendar-week"></i> Check Availability Schedule
                    </button>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('booking.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="facility_id" value="{{ $facility->id }}">

                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="booking_date" class="form-control" 
                                min="{{ date('Y-m-d') }}" max="{{ date('Y-m-d', strtotime('+1 month')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Start Time</label>
                            <select name="start_time" class="form-select" required>
                                <option value="" selected disabled>Select Start Time</option>
                                @for($i = $startH; $i <= $endH; $i++)
                                    @php 
                                        $full = sprintf('%02d:00', $i);
                                        $half = sprintf('%02d:30', $i);
                                    @endphp
                                    @if($full >= $openTime && $full < $closeTime)
                                        <option value="{{ $full }}">{{ $full }}</option>
                                    @endif
                                    @if($half >= $openTime && $half < $closeTime)
                                        <option value="{{ $half }}">{{ $half }}</option>
                                    @endif
                                @endfor
                            </select>
                            <small class="text-muted">Operating hours: {{ $openTime }} - {{ $closeTime }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">End Time</label>
                            <select name="end_time" class="form-select" required>
                                <option value="" selected disabled>Select End Time</option>
                                @for($i = $startH; $i <= $endH; $i++)
                                    @php 
                                        $full = sprintf('%02d:00', $i);
                                        $half = sprintf('%02d:30', $i);
                                    @endphp
                                    @if($full > $openTime && $full <= $closeTime)
                                        <option value="{{ $full }}">{{ $full }}</option>
                                    @endif
                                    @if($half > $openTime && $half <= $closeTime)
                                        <option value="{{ $half }}">{{ $half }}</option>
                                    @endif
                                @endfor
                            </select>
                        </div>
                        @if($users)
                            <div class="alert alert-warning text-center">
                                🚨 **Account Activation in Progress.** <br>
                                Please allow 24 hours for your booking credits to be allocated. You can confirm bookings starting tomorrow.
                            </div>
                        @else
                            <button type="submit" class="btn btn-success w-100 btn-lg">Confirm Booking</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL SECTION -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="scheduleModalLabel">Schedule: {{ $facility->name }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                @include('users.facilities.schedule')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts') <!-- Or just put this before </body> if you don't use stacks -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if there is a 'date' parameter in the URL
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('date')) {
            // Initialize and show the modal automatically
            var myModal = new bootstrap.Modal(document.getElementById('scheduleModal'));
            myModal.show();
        }
    });
</script>
@endpush