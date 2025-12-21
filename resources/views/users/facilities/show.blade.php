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
                    <div class="w-100 h-100 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center">
                        <i class="bi bi-card-image fs-1 text-muted opacity-50"></i>
                    </div>
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

                        <!-- Weekend/Holiday Special Reason (Shows automatically) -->
                        <div id="special-reason-container" class="mb-3 p-3 rounded" style="display: none; background: #fef3c7; border: 2px solid #f59e0b;">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-exclamation-triangle-fill text-warning fs-4 me-2"></i>
                                <div>
                                    <strong class="text-dark" id="special-day-label">Weekend/Holiday Booking</strong>
                                    <br><small class="text-muted" id="special-day-detail">Selected date requires a special reason</small>
                                </div>
                            </div>
                            <label class="form-label fw-bold text-dark">Reason for Booking <span class="text-danger">*</span></label>
                            <textarea name="special_reason" id="special_reason" class="form-control" rows="3" 
                                      placeholder="Please explain why you need to book on this day (minimum 10 characters)..."
                                      minlength="10"></textarea>
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> Weekend and public holiday bookings require admin approval with a valid reason.
                            </small>
                        </div>

                        <!-- Recurring Booking Options -->
                        <div class="mb-3 p-3 rounded" style="background: #f0f9ff; border: 1px solid #bfdbfe;">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_recurring" name="is_recurring" value="1">
                                <label class="form-check-label fw-bold" for="is_recurring">
                                    <i class="bi bi-arrow-repeat text-primary"></i> Make this a recurring booking
                                </label>
                            </div>
                            
                            <div id="recurring-options" style="display: none; margin-top: 1rem;">
                                <div class="mb-3">
                                    <label class="form-label">Repeat Frequency</label>
                                    <select name="recurring_frequency" class="form-select">
                                        <option value="weekly" selected>Weekly (Same day every week)</option>
                                        <option value="daily">Daily</option>
                                        <option value="monthly">Monthly (Same date each month)</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Repeat Until</label>
                                    <input type="date" name="recurring_end_date" class="form-control"
                                           min="{{ date('Y-m-d', strtotime('+1 week')) }}" 
                                           max="{{ date('Y-m-d', strtotime('+3 months')) }}">
                                    <small class="text-muted">Maximum 3 months ahead</small>
                                </div>
                                
                                <div class="alert alert-info py-2 mb-0">
                                    <small><i class="bi bi-info-circle"></i> Credits will be charged for each booking in the recurring series.</small>
                                </div>
                            </div>
                        </div>

                        @if($isNewAccount)
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
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
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
    document.addEventListener('DOMContentLoaded', async function() {
        // Holiday cache in localStorage
        const CACHE_KEY = 'malaysian_holidays_cache';
        const CACHE_DURATION = 24 * 60 * 60 * 1000; // 24 hours
        
        let MALAYSIAN_HOLIDAYS = {};
        
        // Load holidays from API or cache
        async function loadHolidays() {
            // Check localStorage cache first
            const cached = localStorage.getItem(CACHE_KEY);
            if (cached) {
                const cacheData = JSON.parse(cached);
                if (Date.now() - cacheData.timestamp < CACHE_DURATION) {
                    MALAYSIAN_HOLIDAYS = cacheData.holidays;
                    console.log('Holidays loaded from cache');
                    return;
                }
            }
            
            // Fetch from API
            try {
                const currentYear = new Date().getFullYear();
                const nextYear = currentYear + 1;
                
                const [response1, response2] = await Promise.all([
                    fetch(`/api/holidays/${currentYear}`),
                    fetch(`/api/holidays/${nextYear}`)
                ]);
                
                if (response1.ok && response2.ok) {
                    const data1 = await response1.json();
                    const data2 = await response2.json();
                    
                    // Build holidays object
                    const holidays = {};
                    [...data1.holidays, ...data2.holidays].forEach(h => {
                        holidays[h.date] = h.name;
                    });
                    
                    MALAYSIAN_HOLIDAYS = holidays;
                    
                    // Cache in localStorage
                    localStorage.setItem(CACHE_KEY, JSON.stringify({
                        timestamp: Date.now(),
                        holidays: holidays
                    }));
                    
                    console.log('Holidays loaded from API and cached');
                } else {
                    console.warn('Failed to fetch holidays from API, using fallback');
                }
            } catch (error) {
                console.error('Error fetching holidays:', error);
            }
        }
        
        // Load holidays on page load
        await loadHolidays();

        // Elements
        const dateInput = document.querySelector('input[name="booking_date"]');
        const specialReasonContainer = document.getElementById('special-reason-container');
        const specialReasonTextarea = document.getElementById('special_reason');
        const specialDayLabel = document.getElementById('special-day-label');
        const specialDayDetail = document.getElementById('special-day-detail');

        // Check if date is weekend or holiday
        function checkSpecialDay(dateString) {
            if (!dateString) return null;
            
            const date = new Date(dateString);
            const dayOfWeek = date.getDay(); // 0 = Sunday, 6 = Saturday
            
            // Check Weekend
            if (dayOfWeek === 0) {
                return { type: 'weekend', name: 'Sunday' };
            }
            if (dayOfWeek === 6) {
                return { type: 'weekend', name: 'Saturday' };
            }
            
            // Check Holiday from API data
            if (MALAYSIAN_HOLIDAYS[dateString]) {
                return { type: 'holiday', name: MALAYSIAN_HOLIDAYS[dateString] };
            }
            
            return null;
        }

        // Handle date change
        function handleDateChange() {
            const selectedDate = dateInput.value;
            const specialDay = checkSpecialDay(selectedDate);
            
            if (specialDay) {
                if (specialDay.type === 'weekend') {
                    specialDayLabel.textContent = `📅 ${specialDay.name} Booking`;
                    specialDayDetail.textContent = `Weekend bookings require a valid reason for approval.`;
                } else {
                    specialDayLabel.textContent = `🎉 Public Holiday Booking`;
                    specialDayDetail.textContent = `${specialDay.name} - Holiday bookings require a valid reason.`;
                }
                specialReasonContainer.style.display = 'block';
                specialReasonTextarea.required = true;
            } else {
                specialReasonContainer.style.display = 'none';
                specialReasonTextarea.required = false;
                specialReasonTextarea.value = '';
            }
        }

        // Attach event listener
        if (dateInput) {
            dateInput.addEventListener('change', handleDateChange);
            // Check on page load if there's a pre-selected value
            if (dateInput.value) {
                handleDateChange();
            }
        }

        // Check if there is a 'date' parameter in the URL
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('date')) {
            // Initialize and show the modal automatically
            var myModal = new bootstrap.Modal(document.getElementById('scheduleModal'));
            myModal.show();
        }

        // Toggle recurring options
        const recurringCheckbox = document.getElementById('is_recurring');
        const recurringOptions = document.getElementById('recurring-options');
        
        if (recurringCheckbox && recurringOptions) {
            recurringCheckbox.addEventListener('change', function() {
                recurringOptions.style.display = this.checked ? 'block' : 'none';
            });
        }
    });
</script>
@endpush
