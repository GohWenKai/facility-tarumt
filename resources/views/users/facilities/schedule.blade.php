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

<!-- HEADER: Clock & Date Picker -->
<div class="bg-light p-3 border-bottom sticky-top shadow-sm">
    <div class="row align-items-center">
        <!-- Left: Live Clock -->
        <div class="col-md-4">
            <h5 class="m-0 text-primary fw-bold" id="live-clock">{{ now()->format('h:i A') }}</h5>
            <small class="text-muted">{{ now()->format('l, d F Y') }}</small>
        </div>

        <!-- Center: DATE PICKER (Moved Here) -->
        <div class="col-md-4">
            <form action="{{ route('facilities.show', $facility->id) }}" method="GET">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-calendar-event"></i></span>
                    <input type="date" name="date" class="form-control" 
                           value="{{ $currentDate->format('Y-m-d') }}" 
                           onchange="this.form.submit()">
                </div>
            </form>
        </div>

        <!-- Right: Legend -->
        <div class="col-md-4 text-end" style="font-size: 0.8rem;">
            <span class="badge bg-success me-1">Free</span>
            <span class="badge bg-danger me-1">Booked</span>
            <span class="badge bg-warning text-dark">Pending</span>
        </div>
    </div>
</div>

<!-- SCHEDULE TABLE -->
<div class="table-responsive">
    <table class="table table-bordered table-sm text-center mb-0" style="font-size: 0.75rem;">
        <thead class="table-dark text-white">
            <tr>
                <th style="width: 100px; position: sticky; left: 0; z-index: 10;" class="bg-dark">Date</th>
                @foreach($timeSlots as $slot)
                    <th style="min-width: 50px;">{{ $slot }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < 7; $i++)
                @php
                    // Use the selected $currentDate as the starting point
                    $loopDate = $currentDate->copy()->addDays($i);
                    $dateKey = $loopDate->format('Y-m-d');
                    $bookingsForDay = $schedule->get($dateKey);
                    $isToday = $loopDate->isToday();
                @endphp

                <tr>
                    <!-- Date Column -->
                    <td class="align-middle fw-bold {{ $isToday ? 'bg-info text-dark' : 'bg-light' }}" 
                        style="position: sticky; left: 0; z-index: 5;">
                        {{ $loopDate->format('D') }}<br>
                        {{ $loopDate->format('d/m') }}
                    </td>

                    <!-- Time Slots -->
                    @foreach($timeSlots as $slot)
                        @php
                            $status = 'available';
                            $slotTime = \Carbon\Carbon::parse($dateKey . ' ' . $slot);

                            if ($bookingsForDay) {
                                foreach ($bookingsForDay as $bk) {
                                    $bkStart = \Carbon\Carbon::parse($bk->start_time);
                                    $bkEnd   = \Carbon\Carbon::parse($bk->end_time);

                                    if ($slotTime->greaterThanOrEqualTo($bkStart) && $slotTime->lessThan($bkEnd)) {
                                        $status = strtolower($bk->status); 
                                        break; 
                                    }
                                }
                            }

                            if ($slotTime->lessThan(now())) {
                                $status = 'past';
                            }
                        @endphp

                        @if ($status == 'approved')
                            <td class="bg-danger text-white align-middle"><span style="font-size: 8px;"><i class="bi bi-x-lg"></i></span></td>
                        @elseif ($status == 'pending')
                            <td class="bg-warning text-dark align-middle"><span style="font-size: 8px;"><i class="bi bi-hourglass-split"></i></span></td>
                        @elseif ($status == 'past')
                            <td class="bg-secondary text-white-50 align-middle" style="opacity: 0.5;"></td>
                        @else
                            <td class="bg-success text-white align-middle" style="opacity: 0.2;"></td>
                        @endif
                    @endforeach
                </tr>
            @endfor
        </tbody>
    </table>
</div>

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