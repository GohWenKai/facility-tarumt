<?php

namespace App\Patterns\State;

use App\Models\Booking;
use Exception;

class OverdueState implements BookingState
{
    public function approve(Booking $booking)
    {
        throw new Exception("Cannot approve an overdue booking.");
    }

    public function reject(Booking $booking)
    {
        throw new Exception("Cannot reject an overdue booking.");
    }

    public function complete(Booking $booking)
    {
        throw new Exception("Cannot check-in an overdue booking. The time has passed.");
    }
}
