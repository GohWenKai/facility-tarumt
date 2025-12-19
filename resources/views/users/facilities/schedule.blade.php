@php
    $startHour = 8; 
    $endHour = 22; 
    
    // Generate Time Slots
    $timeSlots = [];
    for ($h = $startHour; $h < $endHour; $h++) {
        $timeSlots[] = sprintf('%02d:00', $h);
        $timeSlots[] = sprintf('%02d:30', $h);
    }
@endphp

{{-- ================================================================================= --}}
{{-- PREMIUM STYLES (Scoped to Modal)                                                  --}}
{{-- ================================================================================= --}}
<style>
    .schedule-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: white;
        padding: 1.5rem;
        position: sticky;
        top: 0;
        z-index: 20;
    }
    .schedule-legend .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
    }

    /* SCROLLABLE TABLE AREA */
    .schedule-container {
        max-height: 500px;
        overflow-x: auto;
        overflow-y: auto;
        background: #f8fafc;
    }
    
    /* CUSTOM SCROLLBARS */
    .schedule-container::-webkit-scrollbar { height: 8px; width: 8px; }
    .schedule-container::-webkit-scrollbar-track { background: #f1f5f9; }
    .schedule-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    /* TABLE STYLING */
    .premium-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        margin-bottom: 0;
    }
    .premium-table th, .premium-table td {
        border-right: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
        text-align: center;
        vertical-align: middle;
        position: relative;
    }

    /* STICKY HEADERS logic */
    .premium-table thead th {
        position: sticky;
        top: 0;
        background: #ffffff;
        color: #64748b;
        font-weight: 600;
        font-size: 0.75rem;
        z-index: 10;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        padding: 0.75rem 0.5rem;
    }
    .premium-table .date-col {
        position: sticky;
        left: 0;
        background: #ffffff;
        z-index: 5;
        border-right: 2px solid #e2e8f0;
        width: 80px;
        min-width: 80px;
    }
    .premium-table thead .date-col {
        z-index: 15; /* Top-Left Corner on top of everything */
        background: #f8fafc;
    }

    /* SLOT STATES */
    .slot-cell { height: 40px; min-width: 50px; transition: all 0.1s; }
    
    .slot-approved { background-color: #fee2e2; color: #ef4444; } /* Soft Red */
    .slot-approved:hover { background-color: #fecaca; }

    .slot-pending { 
        background-color: #fef3c7; color: #d97706; /* Soft Orange */
        background-image: repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(255,255,255,0.5) 5px, rgba(255,255,255,0.5) 10px);
    }
    
    .slot-free { background-color: #fff; }
    .slot-free:hover { background-color: #f0f9ff; cursor: pointer; }

    .slot-past { 
        background-color: #f8fafc; 
        background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
        background-size: 4px 4px; 
        opacity: 0.6;
    }

    /* CURRENT DAY HIGHLIGHT */
    .is-today .date-col {
        background-color: #eff6ff !important;
        color: #2563eb;
        border-right: 2px solid #2563eb;
    }
</style>

<!-- 1. HEADER AREA -->
<div class="schedule-header shadow-sm">
    <div class="d-flex justify-content-between align-items-center">
        <!-- Live Clock -->
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-clock-history text-info"></i>
                <h4 class="m-0 fw-bold" id="live-clock" style="width: 130px; letter-spacing: 1px;">
                    {{ now()->format('h:i A') }}
                </h4>
            </div>
            <small class="opacity-75">{{ now()->format('l, d F Y') }}</small>
        </div>

        <!-- Date Picker Widget -->
        <div class="bg-white bg-opacity-10 p-2 rounded d-flex align-items-center gap-2">
            <form action="{{ route('facilities.show', $facility->id) }}" method="GET" class="d-flex align-items-center m-0">
                <label for="date_picker" class="text-white small me-2">Jump to:</label>
                <input type="date" name="date" id="date_picker" 
                       class="form-control form-control-sm border-0 shadow-none" 
                       value="{{ $currentDate->format('Y-m-d') }}" 
                       style="background: rgba(255,255,255,0.9); width: 130px;"
                       onchange="this.form.submit()">
            </form>
        </div>

        <!-- Legend -->
        <div class="text-end schedule-legend">
            <div class="d-flex gap-2">
                <span class="badge bg-white text-success"><i class="bi bi-check-circle me-1"></i>Free</span>
                <span class="badge bg-danger bg-opacity-75"><i class="bi bi-x-circle me-1"></i>Booked</span>
                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass me-1"></i>Pending</span>
            </div>
        </div>
    </div>
</div>

<!-- 2. SCROLLABLE SCHEDULE TABLE -->
<div class="schedule-container">
    <table class="premium-table">
        <thead>
            <tr>
                <!-- Sticky Corner -->
                <th class="date-col text-center p-3">DATE</th>
                
                <!-- Time Slot Headers -->
                @foreach($timeSlots as $slot)
                    <th>{{ $slot }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < 7; $i++)
                @php
                    $loopDate = $currentDate->copy()->addDays($i);
                    $dateKey = $loopDate->format('Y-m-d');
                    $bookingsForDay = $schedule->get($dateKey);
                    $isToday = $loopDate->isToday();
                @endphp

                <tr class="{{ $isToday ? 'is-today' : '' }}">
                    <!-- Date Column -->
                    <td class="date-col fw-bold">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <span class="small text-uppercase text-muted">{{ $loopDate->format('D') }}</span>
                            <span class="fs-5 lh-1">{{ $loopDate->format('d') }}</span>
                        </div>
                    </td>

                    <!-- Time Slots -->
                    @foreach($timeSlots as $slot)
                        @php
                            $status = 'free';
                            $slotTime = \Carbon\Carbon::parse($dateKey . ' ' . $slot);

                            if ($bookingsForDay) {
                                foreach ($bookingsForDay as $bk) {
                                    $bkStart = \Carbon\Carbon::parse($bk->start_time);
                                    $bkEnd   = \Carbon\Carbon::parse($bk->end_time);

                                    // Overlap Check
                                    if ($slotTime->greaterThanOrEqualTo($bkStart) && $slotTime->lessThan($bkEnd)) {
                                        $status = strtolower($bk->status); 
                                        break; 
                                    }
                                }
                            }

                            // Mark Past Slots
                            if ($status == 'free' && $slotTime->lessThan(now())) {
                                $status = 'past';
                            }
                        @endphp

                        @if ($status == 'approved')
                            <td class="slot-cell slot-approved" title="Booked">
                                <i class="bi bi-x-lg small"></i>
                            </td>
                        @elseif ($status == 'pending')
                            <td class="slot-cell slot-pending" title="Has Pending Request">
                                <i class="bi bi-hourglass-split small opacity-50"></i>
                            </td>
                        @elseif ($status == 'past')
                            <td class="slot-cell slot-past" title="Past"></td>
                        @else
                            <td class="slot-cell slot-free" title="Available"></td>
                        @endif
                    @endforeach
                </tr>
            @endfor
        </tbody>
    </table>
</div>

<!-- 3. LIVE CLOCK SCRIPT -->
<script>
    (function(){
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            const clockEl = document.getElementById('live-clock');
            if(clockEl) clockEl.innerText = timeString;
        }
        setInterval(updateClock, 1000);
    })();
</script>