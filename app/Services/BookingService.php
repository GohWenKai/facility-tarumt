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
    protected HolidayService $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

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
        
        // Check public holiday using HolidayService
        $holidayName = $this->holidayService->getHolidayName($dateString);
        if ($holidayName) {
            return [
                'is_off_day' => true,
                'type' => 'holiday',
                'day' => $holidayName,
                'message' => "This is a public holiday ({$holidayName}). Holiday bookings require a special reason."
            ];
        }
        
        return ['is_off_day' => false, 'type' => null, 'day' => null, 'message' => null];
    }

    /**
     * Validate special booking (weekend/holiday)
     * Includes profanity filtering using PurgoMalum API
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
            
            // Check for profanity using PurgoMalum API
            $this->checkProfanity($reason);
        }
    }

    /**
     * Check text for profanity using PurgoMalum API
     * @see https://www.purgomalum.com/
     */
    private function checkProfanity(string $text): void
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->get('https://www.purgomalum.com/service/json', [
                    'text' => $text
                ]);
            
            if ($response->successful()) {
                $result = $response->json();
                $filteredText = $result['result'] ?? $text;
                
                // If the API returned different text, profanity was detected and censored
                if ($filteredText !== $text) {
                    throw new Exception(
                        'Your booking reason contains inappropriate language. Please revise and try again.'
                    );
                }
            }
            // If API fails, we allow the booking to proceed (fail open)
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // API unreachable - log and allow booking to proceed
            \Illuminate\Support\Facades\Log::warning('PurgoMalum API unreachable', ['error' => $e->getMessage()]);
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