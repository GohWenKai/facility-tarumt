<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\BookingService; // Logic Engine
use App\Adapters\XmlTicketAdapter; // XML Parser

class BookingController extends Controller
{
    protected $bookingService;
    protected $ticketAdapter;

    // Dependency Injection
    public function __construct(BookingService $service, XmlTicketAdapter $adapter)
    {
        $this->bookingService = $service;
        $this->ticketAdapter = $adapter;
    }

    // 1. STORE (User Action)
    public function store(Request $request)
    {
        $user = Auth::user();

        // 1. Validation
        $request->validate([
            'facility_id'  => 'required|exists:facilities,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
        ]);

        // 2. Cache Lock (Simple prevention for double-click spam)
        $cacheKey = "booking_lock_{$user->id}";
        if (Cache::has($cacheKey)) {
            return back()->withErrors(['msg' => 'Please wait a moment.']);
        }
        Cache::put($cacheKey, true, 5);

        try {
            // 3. DELEGATE TO SERVICE
            $booking = $this->bookingService->createBooking($user, $request->all());

            return redirect()->route('history')
                ->with('success', "Booking Submitted! Cost: {$booking->total_cost} Credits.");

        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()])->withInput();
        }
    }

    // 2. DOWNLOAD TICKET (Reads XML)
    public function downloadTicket($bookingId)
    {
        // 1. USE ADAPTER TO GET DATA
        $data = $this->ticketAdapter->parseTicket($bookingId);

        if (empty($data)) {
            return back()->with('error', 'Ticket file not found or invalid.');
        }

        // 2. Generate PDF
        $pdf = Pdf::loadView('users.bookings.ticket_pdf', compact('data'));
        return $pdf->download("ticket_{$bookingId}.pdf");
    }

    // 3. CANCEL
    public function cancel($id)
    {
        try {
            $this->bookingService->cancelBooking(Auth::user(), $id);
            return back()->with('success', 'Booking cancelled and credits refunded.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}