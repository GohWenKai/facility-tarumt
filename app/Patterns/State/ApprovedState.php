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
        throw new Exception("Use the reject action instead.");
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