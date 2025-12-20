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
    public function createBooking(User $user, array $data)
    {
        // 1. Prepare Data
        $facility = Facility::findOrFail($data['facility_id']);
        $start = Carbon::parse($data['booking_date'] . ' ' . $data['start_time']);
        $end   = Carbon::parse($data['booking_date'] . ' ' . $data['end_time']);

        // 2. Calculate Cost
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