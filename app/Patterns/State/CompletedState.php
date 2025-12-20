<?php

namespace App\Patterns\State;

use App\Models\Booking;
use Exception;

class CompletedState implements BookingState
{
    public function approve(Booking $booking)
    {
        throw new Exception("Cannot approve a completed booking.");
    }

    public function reject(Booking $booking)
    {
        throw new Exception("Cannot reject a completed booking.");
    }

    public function complete(Booking $booking)
    {
        throw new Exception("Booking is already checked in.");
    }
}
