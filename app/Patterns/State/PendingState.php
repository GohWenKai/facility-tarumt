<?php

namespace App\Patterns\State;

use App\Models\Booking;
use Illuminate\Support\Facades\Storage;
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

        return "Booking Approved & Ticket Generated.";
    }

    public function reject(Booking $booking)
    {
        // 1. Refund Credits
        $booking->user->increment('credits', $booking->total_cost);

        // 2. Update Status
        $booking->status = 'rejected';
        $booking->save();

        return "Booking Rejected. Credits Refunded.";
    }
}