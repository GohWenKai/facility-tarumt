<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Services\BookingContext; // Import Context
use Exception;

class AdminBookingController extends Controller
{
    public function bookings()
    {
        $bookings = Booking::with(['user', 'facility'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('admin.bookings.bookingapproval', compact('bookings'));
    }

    // ACTION 1: APPROVE
    public function approve($id)
    {
        try {
            $booking = Booking::with('user', 'facility')->findOrFail($id);
            
            // PATTERN: STATE
            // 1. Get current state (Pending, Approved, or Rejected)
            $state = BookingContext::getState($booking);
            
            // 2. Execute logic defined in that state class
            $message = $state->approve($booking);

            return back()->with('success', $message);

        } catch (Exception $e) {
            return back()->with('warning', $e->getMessage());
        }
    }

    // ACTION 2: REJECT
    public function reject($id)
    {
        try {
            $booking = Booking::with('user')->findOrFail($id);
            
            // PATTERN: STATE
            $state = BookingContext::getState($booking);
            
            // Execute reject logic (handles refunds/xml deletion automatically)
            $message = $state->reject($booking);

            return back()->with('success', $message);

        } catch (Exception $e) {
            return back()->with('warning', $e->getMessage());
        }
    }

    public function show($id)
    {
        $booking = Booking::with(['user', 'facility'])->findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }
}