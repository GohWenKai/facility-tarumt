<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Services\BookingContext; // Import Context
use App\Services\BookingService;
use Exception;

class AdminBookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $service)
    {
        $this->bookingService = $service;
    }

    public function bookings()
    {
        // Auto-update overdue bookings before showing list
        $this->bookingService->updateOverdueBookings();

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

    // ACTION 3: CHECK-IN (Via QR Code / Scanner)
    public function checkin($id)
    {
        try {
            $booking = Booking::with(['user', 'facility'])->findOrFail($id);
            
            // PATTERN: STATE - Execute complete()
            BookingContext::getState($booking)->complete($booking);

            $status = 'success';
            $message = "Check-in Successful!";
        } catch (Exception $e) {
            $booking = Booking::find($id); // Re-fetch or use null if not found
            $status = 'error';
            $message = $e->getMessage();
        }

        return view('admin.bookings.checkin_result', compact('booking', 'status', 'message'));
    }

    public function show($id)
    {
        $booking = Booking::with(['user', 'facility'])->findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }
}