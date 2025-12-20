<?php

namespace App\Patterns\State;

use App\Models\Booking;
use App\Models\Notification;
use App\Mail\BookingApprovedMail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use SimpleXMLElement;
use Carbon\Carbon;

class PendingState implements BookingState
{
    public function approve(Booking $booking)
    {
        // 1. Update Status
        $booking->status = 'approved';
        $booking->save();

        // 2. LOGIC: Generate XML Ticket (Moved from Controller)
        $ns = 'http://schemas.xmlsoap.org/soap/envelope/';
        $soapEnvelope = new SimpleXMLElement('<soap:Envelope xmlns:soap="'.$ns.'"></soap:Envelope>');
        $soapBody = $soapEnvelope->addChild('Body', null, $ns);
        $ticket = $soapBody->addChild('ticket');

        $ticket->addChild('id', $booking->id);
        $ticket->addChild('student_name', $booking->user->name); 
        $ticket->addChild('role', $booking->user->role); 
        $ticket->addChild('facility', $booking->facility->name);
        $ticket->addChild('start_time', Carbon::parse($booking->start_time)->format('Y-m-d H:i'));
        $ticket->addChild('end_time', Carbon::parse($booking->end_time)->format('Y-m-d H:i'));
        $ticket->addChild('generated_at', now()->toDateTimeString());

        Storage::put("xml/{$booking->id}.xml", $soapEnvelope->asXML());

        // 3. CREATE NOTIFICATION
        Notification::notify(
            $booking->user_id,
            'Booking Approved! ✅',
            "Your booking for {$booking->facility->name} on " . Carbon::parse($booking->start_time)->format('M d, H:i') . " has been approved.",
            'success',
            route('history')
        );

        // 4. SEND EMAIL NOTIFICATION
        try {
            Mail::to($booking->user->email)->send(new BookingApprovedMail($booking));
        } catch (\Exception $e) {
            // Log error but don't fail the approval
            \Log::error('Failed to send booking approval email: ' . $e->getMessage());
        }

        return "Booking Approved & Ticket Generated. Email sent to {$booking->user->email}";
    }

    public function reject(Booking $booking)
    {
        // 1. Refund Credits
        $booking->user->increment('credits', $booking->total_cost);

        // 2. Update Status
        $booking->status = 'rejected';
        $booking->save();

        // 3. CREATE NOTIFICATION
        Notification::notify(
            $booking->user_id,
            'Booking Rejected',
            "Your booking for {$booking->facility->name} was rejected. {$booking->total_cost} credits have been refunded.",
            'danger',
            route('history')
        );

        return "Booking Rejected. Credits Refunded.";
    }

    public function complete(Booking $booking)
    {
        throw new Exception("Booking must be APPROVED by admin before check-in.");
    }
}