<?php

namespace App\Services;

use App\Models\Booking;
use App\Patterns\State\BookingState;
use App\Patterns\State\PendingState;
use App\Patterns\State\ApprovedState;
use App\Patterns\State\RejectedState;

class BookingContext
{
    public static function getState(Booking $booking): BookingState
    {
        return match ($booking->status) {
            'approved' => new ApprovedState(),
            'rejected' => new RejectedState(),
            default    => new PendingState(),
        };
    }
}