<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

use App\Models\CreditTransaction;

class BookingService
{
    // Malaysian Public Holidays (2024-2025)
    private const MALAYSIAN_HOLIDAYS = [
        // 2024
        '2024-01-01' => 'New Year\'s Day',
        '2024-01-25' => 'Thaipusam',
        '2024-02-01' => 'Federal Territory Day',
        '2024-02-10' => 'Chinese New Year',
        '2024-02-11' => 'Chinese New Year (2nd Day)',
        '2024-03-28' => 'Nuzul Al-Quran',
        '2024-04-10' => 'Hari Raya Aidilfitri',
        '2024-04-11' => 'Hari Raya Aidilfitri (2nd Day)',
        '2024-05-01' => 'Labour Day',
        '2024-05-22' => 'Wesak Day',
        '2024-06-03' => 'King\'s Birthday',
        '2024-06-17' => 'Hari Raya Haji',
        '2024-07-07' => 'Awal Muharram',
        '2024-08-31' => 'National Day',
        '2024-09-16' => 'Malaysia Day',
        '2024-09-16' => 'Prophet Muhammad\'s Birthday',
        '2024-10-31' => 'Deepavali',
        '2024-12-25' => 'Christmas Day',
        // 2025
        '2025-01-01' => 'New Year\'s Day',
        '2025-01-14' => 'Thaipusam',
        '2025-01-29' => 'Chinese New Year',
        '2025-01-30' => 'Chinese New Year (2nd Day)',
        '2025-02-01' => 'Federal Territory Day',
        '2025-03-17' => 'Nuzul Al-Quran',
        '2025-03-30' => 'Hari Raya Aidilfitri',
        '2025-03-31' => 'Hari Raya Aidilfitri (2nd Day)',
        '2025-05-01' => 'Labour Day',
        '2025-05-12' => 'Wesak Day',
        '2025-06-02' => 'King\'s Birthday',
        '2025-06-06' => 'Hari Raya Haji',
        '2025-06-27' => 'Awal Muharram',
        '2025-08-31' => 'National Day',
        '2025-09-05' => 'Prophet Muhammad\'s Birthday',
        '2025-09-16' => 'Malaysia Day',
        '2025-10-20' => 'Deepavali',
        '2025-12-25' => 'Christmas Day',
    ];

    /**
     * Check if date is a weekend (Saturday/Sunday) or public holiday
     */
    public function isOffDay(Carbon $date): array
    {
        $dateString = $date->format('Y-m-d');
        
        // Check weekend
        if ($date->isWeekend()) {
            return [
                'is_off_day' => true,
                'type' => 'weekend',
                'day' => $date->format('l'), // Saturday or Sunday
                'message' => "This is a {$date->format('l')}. Weekend bookings require a special reason."
            ];
        }
        
        // Check public holiday
        if (isset(self::MALAYSIAN_HOLIDAYS[$dateString])) {
            return [
                'is_off_day' => true,
                'type' => 'holiday',
                'day' => self::MALAYSIAN_HOLIDAYS[$dateString],
                'message' => "This is a public holiday ({self::MALAYSIAN_HOLIDAYS[$dateString]}). Holiday bookings require a special reason."
            ];
        }
        
        return ['is_off_day' => false, 'type' => null, 'day' => null, 'message' => null];
    }

    /**
     * Validate special booking (weekend/holiday)
     */
    public function validateSpecialBooking(Carbon $date, ?string $reason): void
    {
        $offDayCheck = $this->isOffDay($date);
        
        if ($offDayCheck['is_off_day']) {
            if (empty($reason) || strlen(trim($reason)) < 10) {
                throw new Exception(
                    $offDayCheck['message'] . ' Please provide a valid reason (minimum 10 characters).'
                );
            }
        }
    }

    // Auto-update bookings that passed their end_time to 'overdue'
    public function updateOverdueBookings()
    {
        Booking::whereIn('status', ['pending', 'approved'])
            ->where('end_time', '<', now())
            ->update(['status' => 'overdue']);
    }

    public function createBooking(User $user, array $data)
    {
        // 1. Prepare Data
        $facility = Facility::findOrFail($data['facility_id']);
        $start = Carbon::parse($data['booking_date'] . ' ' . $data['start_time']);
        $end   = Carbon::parse($data['booking_date'] . ' ' . $data['end_time']);

        // 2. Check Weekend/Holiday - Require Special Reason
        $this->validateSpecialBooking($start, $data['special_reason'] ?? null);

        // 3. Calculate Cost
        $cost = (int) max(1, ceil($end->diffInMinutes($start) / 60));

        // 3. Check if Recurring
        $isRecurring = isset($data['is_recurring']) && $data['is_recurring'];
        $recurringDates = [];
        
        if ($isRecurring && !empty($data['recurring_end_date'])) {
            $recurringDates = $this->generateRecurringDates(
                $start,
                $data['recurring_frequency'] ?? 'weekly',
                Carbon::parse($data['recurring_end_date'])
            );
            
            // Total cost for all bookings
            $totalCost = $cost * (count($recurringDates) + 1);
            
            if ($user->credits < $totalCost) {
                $bookingCount = count($recurringDates) + 1;
                throw new Exception("Insufficient credits for $bookingCount recurring bookings. Need $totalCost, have {$user->credits}.");
            }
        } else {
            if ($user->credits < $cost) {
                throw new Exception("Insufficient credits. You need $cost, but have {$user->credits}.");
            }
        }

        // 4. ATOMIC TRANSACTION (The Engine)
        return DB::transaction(function () use ($user, $facility, $start, $end, $cost, $isRecurring, $recurringDates, $data) {
            
            // Check for overlaps for primary booking
            $this->checkOverlap($facility->id, $start, $end);

            // Check if this is a special day (weekend/holiday)
            $offDayCheck = $this->isOffDay($start);

            // Create Parent Booking
            $parentBooking = Booking::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'facility_id' => $facility->id,
                'start_time' => $start,
                'end_time' => $end,
                'total_cost' => $cost,
                'status' => 'pending',
                'is_recurring' => $isRecurring,
                'recurring_frequency' => $data['recurring_frequency'] ?? null,
                'recurring_end_date' => $data['recurring_end_date'] ?? null,
                'special_reason' => $data['special_reason'] ?? null,
                'is_special_day' => $offDayCheck['is_off_day'],
            ]);

            // Deduct Credits for parent
            $user->decrement('credits', $cost);
            
            // LOG PARENT TRANSACTION
            CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => -$cost,
                'type' => 'booking',
                'description' => "Booking for {$facility->name} ({$start->format('d M H:i')})",
                'related_id' => $parentBooking->id
            ]);

            // Create Child Bookings for recurring
            if ($isRecurring && count($recurringDates) > 0) {
                foreach ($recurringDates as $recurringStart) {
                    $recurringEnd = $recurringStart->copy()->addMinutes($end->diffInMinutes($start));
                    
                    // Check overlap for each recurring date
                    $this->checkOverlap($facility->id, $recurringStart, $recurringEnd);
                    
                    // Check if this recurring date is a special day
                    $recurringOffDay = $this->isOffDay($recurringStart);
                    
                    // For recurring bookings on special days, require reason from parent
                    if ($recurringOffDay['is_off_day'] && empty($data['special_reason'])) {
                        throw new Exception(
                            "Recurring booking includes {$recurringOffDay['day']} ({$recurringStart->format('d M Y')}). " .
                            "Please provide a special reason for weekend/holiday bookings."
                        );
                    }
                    
                    $childBooking = Booking::create([
                        'id' => (string) Str::uuid(),
                        'user_id' => $user->id,
                        'facility_id' => $facility->id,
                        'start_time' => $recurringStart,
                        'end_time' => $recurringEnd,
                        'total_cost' => $cost,
                        'status' => 'pending',
                        'is_recurring' => true,
                        'recurring_frequency' => $data['recurring_frequency'] ?? null,
                        'parent_booking_id' => $parentBooking->id,
                        'special_reason' => $recurringOffDay['is_off_day'] ? ($data['special_reason'] ?? null) : null,
                        'is_special_day' => $recurringOffDay['is_off_day'],
                    ]);
                    
                    $user->decrement('credits', $cost);
                    
                    // LOG CHILD TRANSACTION
                    CreditTransaction::create([
                        'user_id' => $user->id,
                        'amount' => -$cost,
                        'type' => 'booking',
                        'description' => "Recurring Booking for {$facility->name} ({$recurringStart->format('d M H:i')})",
                        'related_id' => $childBooking->id
                    ]);
                }
            }

            return $parentBooking;
        });
    }

    private function generateRecurringDates(Carbon $startDate, string $frequency, Carbon $endDate): array
    {
        $dates = [];
        $current = $startDate->copy();
        
        while (true) {
            // Move to next occurrence
            $current = match($frequency) {
                'daily' => $current->addDay(),
                'weekly' => $current->addWeek(),
                'monthly' => $current->addMonth(),
                default => $current->addWeek(),
            };
            
            // Stop if past end date
            if ($current->startOfDay()->gt($endDate->startOfDay())) {
                break;
            }
            
            // Max 12 occurrences for safety
            if (count($dates) >= 12) {
                break;
            }
            
            $dates[] = $current->copy();
        }
        
        return $dates;
    }

    private function checkOverlap($facilityId, Carbon $start, Carbon $end): void
    {
        $exists = Booking::where('facility_id', $facilityId)
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($start, $end) {
                $query->where('start_time', '<', $end)
                      ->where('end_time', '>', $start);
            })
            ->lockForUpdate()
            ->exists();

        if ($exists) {
            throw new Exception("Time slot conflict on {$start->format('Y-m-d H:i')}. Please choose a different time.");
        }
    }

    public function cancelBooking(User $user, $bookingId)
    {
        $booking = Booking::where('id', $bookingId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($booking->status !== 'pending') {
            throw new Exception('You can only cancel pending bookings.');
        }

        // Prevent cancelling past bookings
        if ($booking->start_time->lt(now())) {
            throw new Exception('You cannot cancel a booking that has already started.');
        }

        DB::transaction(function() use ($user, $booking) {
            $user->increment('credits', $booking->total_cost); // Refund
            $booking->delete();
            
            // LOG REFUND TRANSACTION
            CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => $booking->total_cost,
                'type' => 'refund',
                'description' => "Refund for cancelled booking #{$booking->id}",
                'related_id' => $booking->id // Note: Booking is deleted, so UUID is just for record
            ]);
        });

        return true;
    }
}