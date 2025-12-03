<?php

namespace App\Patterns\State;

use App\Models\Booking;
use Illuminate\Support\Facades\Storage;
use Exception;

class ApprovedState implements BookingState
{
    public function approve(Booking $booking)
    {
        throw new Exception("This booking is already approved.");
    }

    public function reject(Booking $booking)
    {
        // ADMIN REGRET LOGIC
        
        // 1. Refund Credits
        $booking->user->increment('credits', $booking->total_cost);

        // 2. Update Status
        $booking->status = 'rejected';
        $booking->save();

        // 3. LOGIC: Delete XML (Revoke Ticket)
        $path = "xml/{$booking->id}.xml";
        if (Storage::exists($path)) {
            Storage::delete($path);
        }

        return "Booking Rejected (Reversed). Credits Refunded & Ticket Deleted.";
    }
}