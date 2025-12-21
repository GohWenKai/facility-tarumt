<?php

namespace App\Patterns\State;

use App\Models\Booking;
use App\Models\Notification;
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
        // 1. Delete the XML ticket if it exists
        $xmlPath = "xml/{$booking->id}.xml";
        if (Storage::exists($xmlPath)) {
            Storage::delete($xmlPath);
        }

        // 2. Refund Credits to user (Robust reload)
        $user = $booking->user; // Get user model
        if ($user) {
            $user->refresh(); // Ensure latest balance
            $user->credits += (int) $booking->total_cost;
            $user->save();
        }

        // 3. Update Status to rejected (revoked)
        $booking->status = 'rejected';
        $booking->save();

        // 4. Create Notification for user
        Notification::notify(
            $booking->user_id,
            'Booking Revoked/Cancelled',
            "Your approved booking for {$booking->facility->name} has been revoked by admin. {$booking->total_cost} credits have been refunded.",
            'warning',
            route('history')
        );

        return "Booking Revoked. XML Ticket deleted and credits refunded.";
    }

    public function complete(Booking $booking)
    {
        $now = now();

        // 1. Check if too early
        if ($now->lt($booking->start_time)) {
            throw new Exception("Check-in Failed: Booking has not started yet. Starts at " . $booking->start_time->format('Y-m-d H:i'));
        }

        // 2. Check if too late
        if ($now->gt($booking->end_time)) {
            throw new Exception("Check-in Failed: Booking ended at " . $booking->end_time->format('Y-m-d H:i'));
        }

        $booking->status = 'completed';
        $booking->save();

        return "Check-in Successful! Welcome.";
    }
}
