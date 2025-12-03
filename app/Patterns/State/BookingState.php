<?php

namespace App\Patterns\State;

use App\Models\Booking;

interface BookingState
{
    public function approve(Booking $booking);
    public function reject(Booking $booking);
}