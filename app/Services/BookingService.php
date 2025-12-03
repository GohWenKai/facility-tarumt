<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

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

        // 3. Check Credits
        if ($user->credits < $cost) {
            throw new Exception("Insufficient credits. You need $cost, but have {$user->credits}.");
        }

        // 4. ATOMIC TRANSACTION (The Engine)
        return DB::transaction(function () use ($user, $facility, $start, $end, $cost) {
            
            // PESSIMISTIC LOCKING: Lock the rows to prevent double booking
            // We check for overlaps while locking the table logic
            $exists = Booking::where('facility_id', $facility->id)
                ->where('status', '!=', 'rejected')
                ->where(function ($query) use ($start, $end) {
                    $query->where('start_time', '<', $end)
                          ->where('end_time', '>', $start);
                })
                ->lockForUpdate() // <--- CRITICAL: Prevents race conditions
                ->exists();

            if ($exists) {
                throw new Exception('This time slot is already booked by someone else.');
            }

            // Create Booking
            $booking = Booking::create([
                'id' => (string) Str::uuid(), // Generate UUID
                'user_id' => $user->id,
                'facility_id' => $facility->id,
                'start_time' => $start,
                'end_time' => $end,
                'total_cost' => $cost,
                'status' => 'pending',
            ]);

            // Deduct Credits
            $user->decrement('credits', $cost);

            return $booking;
        });
    }

    public function cancelBooking(User $user, $bookingId)
    {
        $booking = Booking::where('id', $bookingId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($booking->status !== 'pending') {
            throw new Exception('You can only cancel pending bookings.');
        }

        DB::transaction(function() use ($user, $booking) {
            $user->increment('credits', $booking->total_cost); // Refund
            $booking->delete();
        });

        return true;
    }
}