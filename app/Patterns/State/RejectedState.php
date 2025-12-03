<?php

namespace App\Patterns\State;

use App\Models\Booking;
use Exception;

class RejectedState implements BookingState
{
    public function approve(Booking $booking)
    {
        throw new Exception("This booking was rejected. Student must book again.");
    }

    public function reject(Booking $booking)
    {
        throw new Exception("Already rejected.");
    }
}