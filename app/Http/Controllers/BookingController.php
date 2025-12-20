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
        $rules = [
            'facility_id'  => 'required|exists:facilities,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
        ];

        // Add recurring validation if checkbox is checked
        if ($request->has('is_recurring')) {
            $rules['recurring_frequency'] = 'required|in:daily,weekly,monthly';
            $rules['recurring_end_date'] = 'required|date|after:booking_date';
        }

        $request->validate($rules);

        // 2. Cache Lock (Simple prevention for double-click spam)
        $cacheKey = "booking_lock_{$user->id}";
        if (Cache::has($cacheKey)) {
            return back()->withErrors(['msg' => 'Please wait a moment.']);
        }
        Cache::put($cacheKey, true, 5);

        try {
            // 3. DELEGATE TO SERVICE
            $booking = $this->bookingService->createBooking($user, $request->all());

            // Count total bookings created (parent + children)
            $childCount = $booking->childBookings()->count();
            $totalCount = $childCount + 1;
            $totalCost = $booking->total_cost * $totalCount;

            if ($childCount > 0) {
                $message = "Recurring Booking Created! {$totalCount} bookings for {$totalCost} credits total.";
            } else {
                $message = "Booking Submitted! Cost: {$booking->total_cost} Credits.";
            }

            return redirect()->route('history')->with('success', $message);

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
        $pdf = Pdf::loadView('users.bookings.ticket_pdf', compact('data'))
            ->setOptions(['isRemoteEnabled' => true]);
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

    // 4. HISTORY
    public function history(Request $request)
    {
        // Auto-update overdue bookings before showing history
        $this->bookingService->updateOverdueBookings();

        $user = Auth::user();
        $query = Booking::with('facility')->where('user_id', $user->id);

        // Optional filtering
        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('start_time', $request->date);
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        return view('users.bookings.history', compact('bookings'));
    }
}